<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Branch;
use App\Setting;


use App\BankCash;
use App\Transaction;
use App\Helpers\Helper;
use App\IncomeExpenseHead;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Session;
use App\Exports\ReceivePayment\BranchWise;

class ReceivePaymentController extends Controller
{
    public function index()
    {
        return view('admin.accounts-report.receive-and-payment.index', Helper::__getBranchBankCashIncomeExpenseHead());
    }

    /**
     * This function return receive payment
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/09/2022
     * Time         12:19:21
     * @param       $request
     * @return      
     */
    public function branch_wise(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Receive And Payment Report',
            'voucher_type' => 'RECEIVE AND PAYMENT REPORT'
        );
        // Trial balance and receive payment has little different
        // Trial balance has all voucher of transaction
        // Receive payment has all voucher of transaction except journal voucher

        $transaction_query = Transaction::query();
        $transaction_query->whereNotIn('voucher_type', ['JV']);
        if ($request->branch_id) {
            $transaction_query->where('branch_id', $request->branch_id);
        }
        if ($request->from && $request->to) {
            $transaction_query->whereBetween('voucher_date', [date("Y-m-d", strtotime($request->from)), date("Y-m-d", strtotime($request->to))]);
        }
        $transaction_query->with('Branch', 'IncomeExpenseHead', 'BankCash');
        $transactions = $transaction_query->orderBy('voucher_date', 'DESC')->get();
        $branches = $transactions->groupBy('Branch.name');
        $bank_cashes = $transactions->whereNotNull('bank_cash_id')->groupBy('BankCash.name');
        $search_by = array(
            'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : null,
            'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : null,
        );
        $items['branches'] = $branches;
        $items['bank_cashes'] = $bank_cashes;
        if ($branches->count() < 1 && $bank_cashes->count() < 1) {
            Session::flash('error', 'There Has No Transaction');
            return redirect()->back();
        }
        // Common items
        $search_by = array(
            'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : null,
            'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : null,
        );
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.receive-and-payment.branch-wise.index')
                ->with('items', $items)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Pdf Action
        if ($request->action == 'Pdf') {

            $pdf = PDF::loadView('admin.accounts-report.receive-and-payment.branch-wise.pdf', [
                'items' => $items,
                'extra' => $extra,
                'search_by' => $search_by,
            ])
                ->setPaper('a4', 'landscape');

            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWise([
                'items' => $items,
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    public function GetReceivePaymentByBranchIdIncExpIdTypeId($branch_id, $head_id, $type_id, $from_date = null, $to_date = null)
    {
        if (!empty($from_date)) {
            $condition = "branch_id=" . $branch_id . " AND income_expense_head_id =" . $head_id . " AND type=" . $type_id . " 
            AND voucher_date BETWEEN '" . date("Y-m-d", strtotime($from_date)) . "' AND '" . date("Y-m-d", strtotime($to_date)) . "' ";
        } else {
            $condition = " branch_id=" . $branch_id . " AND income_expense_head_id =" . $head_id . " AND type=" . $type_id . " ";
        }

        $DrCrDetails = DB::select(DB::raw("
             SELECT transactions.dr , transactions.cr 
             FROM 
             transactions 
             INNER JOIN income_expense_heads
             ON transactions.income_expense_head_id=income_expense_heads.id
             WHERE " . $condition . " AND voucher_type NOT IN ('JV')
             AND transactions.deleted_at IS NULL
            ;
        "));

        $balance = 0;
        foreach ($DrCrDetails as $crDetail) {
            if ($type_id == 1) { /// Dr
                $balance += $crDetail->dr - $crDetail->cr;
            } else {  // Cr
                $balance += $crDetail->cr - $crDetail->dr;
            }
        }
        return $balance;
    }
}

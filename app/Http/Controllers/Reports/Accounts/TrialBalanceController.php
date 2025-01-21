<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\BankCash;
use App\Branch;
use App\Transaction;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Exports\TrialBalance\BranchWise;
use App\Http\Controllers\CrVoucherController;
use App\IncomeExpenseHead;

class TrialBalanceController extends Controller
{
    public function index()
    {
        $crvoucher = new CrVoucherController();
        return view('admin.accounts-report.trial-balance.index', $crvoucher->__getBranchBankCashIncomeExpenseHead());
    }

    public function branch_wise(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Trial Balance Report',
            'voucher_type' => 'TRIAL BALANCE REPORT'
        );
        
        $transaction_query = Transaction::query();
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
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.trial-balance.branch-wise.index')
                ->with('items', $items)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.trial-balance.branch-wise.pdf', [
                'items' => $items,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
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
}

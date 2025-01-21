<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Exports\Notes\LedgerGroupWise;
use App\Exports\Notes\LedgerTypeWise;
use App\IncomeExpenseGroup;
use App\IncomeExpenseType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Branch;
use App\Helpers\Helper;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;


class NotesController extends Controller
{
    public function index()
    {
        $data = Helper::__getBranchBankCashIncomeExpenseHead();
        return view('admin.accounts-report.notes.index', $data);
    }

    public function type_wise(Request $request)
    {
        $request->validate([
            'start_from' => 'nullable',
            'start_to' => 'nullable',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Notes Type',
            'voucher_type' => 'NOTES'
        );

        $transaction_details = Helper::type_wise_transaction_details($request->income_expense_type_id, $request->branch_id, $request->start_from, $request->start_to);
        // Common items
        if ($transaction_details->count() < 1) {
            Session::flash('error', 'There Has No Transaction');
            return redirect()->back();
        }

        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $income_expense_types = Helper::__getBranchBankCashIncomeExpenseHead()['income_expense_types'];
        $search_by = array(
            'branch_name' => ($request->branch_id) ? $branches->where('id', $request->branch_id)->first()->name : 'All Branch',
            'type_name' => ($request->income_expense_type_id) ? $income_expense_types->where('id', $request->income_expense_type_id)->first()->name : 'All types',
            'start_from' => ($request->start_from) ?  date(config('settings.date_format'), strtotime($request->start_from)) : null,
            'start_to' => ($request->start_to) ?  date(config('settings.date_format'), strtotime($request->start_to)) : null
        );

        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.notes.type-wise.index')
                ->with('particulars', $transaction_details)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.notes.type-wise.pdf', [
                'particulars' => $transaction_details,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            // return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new LedgerTypeWise([
                'particulars' => $transaction_details,
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }

    public function group_wise(Request $request)
    {
        $request->validate([
            'end_from1' => 'nullable',
            'end_to1' => 'nullable',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Ledger Group',
            'voucher_type' => 'NOTES'
        );
        
        $transaction_details = Helper::group_wise_transaction_details($request->income_expense_group_id, $request->branch_id, $request->start_from, $request->start_to);
        // Common items
        if ($transaction_details->count() < 1) {
            Session::flash('error', 'There Has No Transaction');
            return redirect()->back();
        }

        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $income_expense_groups = Helper::__getBranchBankCashIncomeExpenseHead()['income_expense_groups'];
        $search_by = array(
            'branch_name' => ($request->branch_id) ? $branches->where('id', $request->branch_id)->first()->name : 'All Branch',
            'group_name' => ($request->income_expense_group_id) ? $income_expense_groups->where('id', $request->income_expense_group_id)->first()->name : 'All Groups',
            'start_from' => ($request->start_from) ?  date(config('settings.date_format'), strtotime($request->start_from)) : null,
            'start_to' => ($request->start_to) ?  date(config('settings.date_format'), strtotime($request->start_to)) : null
        );
        
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.notes.group-wise.index')
                ->with('particulars', $transaction_details)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.notes.group-wise.pdf', [
                'particulars' => $transaction_details,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }
        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new LedgerGroupWise([
                'particulars' => $transaction_details,
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }
}

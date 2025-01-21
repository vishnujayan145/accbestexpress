<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\BankCash;
use App\Branch;
use App\Exports\Ledger\BranchWiseLedger;
use App\Exports\Ledger\BankCashWise;
use App\Exports\Ledger\IncomeExpenseHeadWise;
use App\Helpers\Helper;
use App\IncomeExpenseHead;
use App\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class AccountsReportController extends Controller
{
    public function ledger_index()
    {
        $data['bank_cashes'] = BankCash::all();
        $data['shipments'] =  DB::connection('mysql2')
            ->table('shipments')
            ->join('customers', 'shipments.sender_id', '=', 'customers.id')
            ->selectRaw('MAX(shipments.id) as id, MAX(shipments.branch_id) as branch_id, MAX(shipments.payment_method) as payment_method, MAX(shipments.balance) as balance, customers.name as sender,MAX(shipments.sender_id) as sender_id')
            ->where('shipments.payment_method', 'credit')
            ->orWhere('shipments.balance', '!=', 0)
            ->groupBy('customers.name')
            ->get();
        return view('admin.accounts-report.ledger.index', $data, $this->__getBranchBankCashIncomeExpenseHead());
        //$crvoucher = new CrVoucherController();
        //return view('admin.accounts-report.ledger.index', $crvoucher->__getBranchBankCashIncomeExpenseHead());
    }

    /**
     * This function return branch wisse ledger report
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/19/2022
     * Time         14:46:27
     * @param       $request
     * @return      
     */
    public function ledger_branch_wise_report(Request $request)
    {
        $branch_module = '';
        $incomeExpenseHead = '';
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Branch Wise ledger Report',
            'voucher_type' => 'BRANCH WISE LEDGER REPORT'
        );
        $branch_query = Branch::query();
        $branch_query->with('Transaction', 'Transaction.IncomeExpenseHead', 'Transaction.BankCash');
        if ($request->branch_id != null) {
            $branch_query->where('id', $request->branch_id);
            $branch_module = Branch::find($request->branch_id);
        }
        if ($request->income_expense_head_id != null) {
            $incomeExpenseHead = IncomeExpenseHead::find($request->income_expense_head_id);
        }
        $branch_query->with(['Transaction' => function ($query) use ($request) {
            if ($request->income_expense_head_id != null) {
                $query->where('income_expense_head_id', $request->income_expense_head_id);
            }
            if ($request->from != null && $request->to != null) {
                $query->whereBetween('voucher_date', array(date("Y-m-d", strtotime($request->from)), date("Y-m-d", strtotime($request->to))));
            }
        }]);
        $search_by = array(
            'branch_name' => ($branch_module) ? $branch_module->name : '',
            'income_expense_head_name' => ($incomeExpenseHead) ? $incomeExpenseHead->name : '',
            'from' => ($request->from && $request->from != null) ? date(config('settings.date_format'), strtotime($request->from)) : '',
            'to' => ($request->to && $request->to != null) ? date(config('settings.date_format'), strtotime($request->to)) : '',
        );
        //Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.ledger.branch-wise.index')
                ->with('items', $branch_query->get())
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            set_time_limit(300);
            $pdf = PDF::loadView('admin.accounts-report.ledger.branch-wise.pdf', [
                'items' => $branch_query->get(),
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
        }
        //  Exl Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWiseLedger([
                'items' => $branch_query->get(),
                'extra' => $extra,
            ]);
            return Excel::download($BranchWise, date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.xlsx');
        }
    }
    #end

    /**
     * This function return income expense head wise report
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/19/2022
     * Time         16:52:42
     * @param       $request
     * @return      
     */
    public function ledger_income_expense_head_wise_report(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Report',
            'voucher_type' => 'LEDGER WISE REPORT'
        );

        // Initialize the query
        $income_expense_head_query = IncomeExpenseHead::query();
        $income_expense_head_query->with('Transaction', 'Transaction.Branch', 'Transaction.BankCash');

        // Filter by income expense head if provided
        if ($request->income_expense_head_id && $request->income_expense_head_id != null) {
            $income_expense_head_query->where('id', $request->income_expense_head_id);
        }

        // Define the date range for the report
        $fromDate = $request->from ? date("Y-m-d", strtotime($request->from)) : null;
        $toDate = $request->to ? date("Y-m-d", strtotime($request->to)) : null;


        // Calculate the end of the day before the 'from' date
        $endOfPreviousDay = \Carbon\Carbon::parse($fromDate)->subDay()->endOfDay();

        // Initialize search_by array
        $search_by = array(
            'branch_name' => ($request->branch_id != null) ? Branch::find($request->branch_id)->name : '',
            'income_expense_head_name' => '',
            'opening_balance' => 0,
            'type' => '',
            'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : '',
            'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : '',
        );

        // Fetch the opening balance for the selected income expense head
        if ($request->income_expense_head_id) {
            $incomeExpenseHead = IncomeExpenseHead::find($request->income_expense_head_id);

            if ($incomeExpenseHead) {
                // Retrieve stored opening balance
                $openingBalance = $incomeExpenseHead->opening_balance;
                $type = $incomeExpenseHead->type;
                // If type is 1, set the opening balance as negative
                if ($type == 1) {
                    $openingBalance = -abs($openingBalance); // Ensure it's negative
                }

                // Fetch transactions up to the end of the day before the 'from' date
                $transactions = Transaction::where('income_expense_head_id', $incomeExpenseHead->id)
                    ->where('voucher_date', '<=', $endOfPreviousDay)
                    ->orderBy('voucher_date', 'asc') // Ascending order
                    ->get();

                // Adjust the opening balance based on transactions
                foreach ($transactions as $transaction) {
                    if ($transaction->type) {
                        $openingBalance += - ($transaction->dr - $transaction->cr);
                    } else {
                        $openingBalance += $transaction->cr - $transaction->dr;
                    }
                }

                // Update search_by array with the computed values
                $search_by['income_expense_head_name'] = $incomeExpenseHead->name;
                $search_by['opening_balance'] = $openingBalance;
                $search_by['type'] = $type;
            }
        }

        // Apply additional filters to transactions
        $income_expense_head_query->with(['Transaction' => function ($query) use ($request, $fromDate, $toDate) {
            if ($request->branch_id && $request->branch_id != null) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($fromDate && $toDate) {
                $query->whereBetween('voucher_date', [$fromDate, $toDate]);
            }
            // Order transactions by voucher_date in ascending order
            $query->orderBy('voucher_date', 'asc');
        }]);

        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.ledger.income-expense-head-wise.index')
                ->with('items', $income_expense_head_query->get())
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.ledger.income-expense-head-wise.pdf', [
                'items' => $income_expense_head_query->get(),
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $IncomeExpenseHeadWise = new IncomeExpenseHeadWise([
                'items' => $income_expense_head_query->get(),
                'extra' => $extra,
            ]);
            return Excel::download($IncomeExpenseHeadWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    public function ledger_cash_receivables_report(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Report',
            'voucher_type' => 'CASH RECEIVABLES REPORT'
        );
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $branch_name = '';

        if ($request->branch_id) {
            $branch = $branches->where('id', $request->branch_id)->first();
            if ($branch) {
                $branch_name = $branch->name;
            }
        }

        // Define the date range for the report
        $fromDate = $request->from ? date("Y-m-d", strtotime($request->from)) : null;
        $toDate = $request->to ? date("Y-m-d", strtotime($request->to)) : null;
        // Initialize the query
        $shipmentsQuery = DB::connection('mysql2')
            ->table('shipments')
            ->join('branches', 'shipments.branch_id', '=', 'branches.id')
            ->join('customers', 'shipments.sender_id', '=', 'customers.id')
            ->select('shipments.*', 'branches.name as branch_name', 'customers.name as sender')
            ->where(function ($query) {
                $query->where('shipments.payment_method', 'credit')
                    ->orWhere('shipments.balance', '!=', 0);
            })
            ->orderBy('shipments.created_at', 'asc'); // Ensure correct table reference

        // Filter by income expense head if provided
        if ($request->income_expense_head_id && $request->income_expense_head_id != null) {
            $shipmentsQuery->where('shipments.sender_id', $request->income_expense_head_id);
        }

        // Use `branches.name` explicitly to avoid ambiguity
        if (!empty($branch_name)) {
            $shipmentsQuery->where('branches.name', $branch_name);
        }

        // Add the date range filter only if `$fromDate` and `$toDate` are provided
        if (!empty($fromDate) && !empty($toDate)) {
            $shipmentsQuery->whereBetween('shipments.created_at', [$fromDate, $toDate]);
        }

        // Execute the query and fetch the results
        $shipments = $shipmentsQuery->get();
        // Handle income_expense_head_name safely by checking if any shipments exist
        $firstShipment = $shipments->first(); // Get the first row if it exists
        $search_by = array(
            'branch_name' => ($request->branch_id != null) ? Branch::find($request->branch_id)->name : '',
            'income_expense_head_name' => ($request->income_expense_head_id != null) ? $firstShipment ? $firstShipment->sender : '' : '',
            'opening_balance' => 0,
            'type' => '',
            'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : '',
            'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : '',
        );
        // Initialize the query
        $income_expense_head_query = IncomeExpenseHead::query();
        $income_expense_head_query->with('Transaction', 'Transaction.Branch', 'Transaction.BankCash');
        // Apply additional filters to transactions
        $income_expense_head_query->with(['Transaction' => function ($query) use ($request, $fromDate, $toDate) {
            if ($request->branch_id && $request->branch_id != null) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($fromDate && $toDate) {
                $query->whereBetween('voucher_date', [$fromDate, $toDate]);
            }
            // Order transactions by voucher_date in ascending order
            $query->orderBy('voucher_date', 'asc');
        }]);

        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.ledger.cash-receivables.index')
                ->with('items', $income_expense_head_query->get())
                ->with('shipments', $shipments)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $IncomeExpenseHeadWise = new IncomeExpenseHeadWise([
                'items' => $income_expense_head_query->get(),
                'shipments' => $shipments,
                'extra' => $extra,
            ]);
            return Excel::download($IncomeExpenseHeadWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    /*  public function ledger_income_expense_head_wise_report(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Report',
            'voucher_type' => 'LEDGER WISE REPORT'
        );
        $income_expense_head_query = IncomeExpenseHead::query();
        $income_expense_head_query->with('Transaction', 'Transaction.Branch', 'Transaction.BankCash');
        if ($request->income_expense_head_id && $request->income_expense_head_id != null) {
            $income_expense_head_query->where('id', $request->income_expense_head_id);
        }
        $income_expense_head_query->with(['Transaction' => function ($query) use ($request) {
            if ($request->branch_id && $request->branch_id  != null) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->from != null && $request->to != null) {
                $query->whereBetween('voucher_date', array(date("Y-m-d", strtotime($request->from)), date("Y-m-d", strtotime($request->to))));
            }
        }]);
        $search_by = array(
            'branch_name' => ($request->branch_id != null) ? Branch::find($request->branch_id)->name : '',
            'income_expense_head_name' => ($request->income_expense_head_id != null) ? IncomeExpenseHead::find($request->income_expense_head_id)->name : '',
            'opening_balance' => ($request->income_expense_head_id != null) ? IncomeExpenseHead::find($request->income_expense_head_id)->opening_balance : '',
            'type' => ($request->income_expense_head_id != null) ? IncomeExpenseHead::find($request->income_expense_head_id)->type : '',
            'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : '',
            'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : '',
        );
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.ledger.income-expense-head-wise.index')
                ->with('items', $income_expense_head_query->get())
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.ledger.income-expense-head-wise.pdf', [
                'items' => $income_expense_head_query->get(),
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }
        // Excel Action
        if ($request->action == 'Excel') {
            $IncomeExpenseHeadWise = new IncomeExpenseHeadWise([
                'items' => $income_expense_head_query->get(),
                'extra' => $extra,
            ]);
            return Excel::download($IncomeExpenseHeadWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }
    */
    #end

    /**
     * This function return ledger by bank cash wise
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/19/2022
     * Time         18:37:28
     * @param       $request
     * @return      
     */
    public function ledger_bank_cash_wise_report(Request $request)
    {
        $now = new \DateTime();
        $date = $now->format(config('settings.date_format') . ' h:i:s');
        $extra = [
            'current_date_time' => $date,
            'module_name' => 'Bank Cash Wise',
            'voucher_type' => 'BANK CASH WISE LEDGER REPORT'
        ];

        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $bank_cashes = Helper::__getBranchBankCashIncomeExpenseHead()['bank_cashes'];

        // Fetch branch and bank cash names with null checks
        $branch_name = '';
        $bank_cash_name = '';

        if ($request->branch_id) {
            $branch = $branches->where('id', $request->branch_id)->first();
            if ($branch) {
                $branch_name = $branch->name;
            }
        }

        if ($request->bank_cash_id) {
            $bank_cash = BankCash::find($request->bank_cash_id);
            if ($bank_cash) {
                $bank_cash_name = $bank_cash->name;
            }
        }

        // Handle date range: 'from' and 'to'
        $from = $request->from ? date('Y-m-d', strtotime($request->from)) : '';
        $to = $request->to ? date('Y-m-d', strtotime($request->to)) : '';

        // Calculate the end of the day before the 'from' date
        $opening_balance_date = \Carbon\Carbon::parse($from)->subDay()->endOfDay();
        // Fetch the opening balance for the selected income expense head
        if ($request->bank_cash_id) {
            $BankCash = BankCash::find($request->bank_cash_id);

            if ($BankCash) {
                // Retrieve stored opening balance
                $openingBalance = $BankCash->opening_balance;
                $type = $BankCash->type;
                // If type is 1, set the opening balance as negative
                if ($type == 1) {
                    $openingBalance = -abs($openingBalance); // Ensure it's negative
                }
                // Fetch transactions up to the end of the day before the 'from' date
                $transactions = Transaction::where('bank_cash_id', $BankCash->id)
                    ->where('voucher_date', '<=', $opening_balance_date)
                    ->orderBy('voucher_date', 'asc') // Ascending order
                    ->get();

                // Adjust the opening balance based on transactions
                foreach ($transactions as $transaction) {
                    if ($transaction->type) {
                        $openingBalance += - ($transaction->dr - $transaction->cr);
                    } else {
                        $openingBalance += $transaction->cr - $transaction->dr;
                    }
                }

                // Update search_by array with the computed values
                $search_by = [
                    'branch_name' => $branch_name,
                    'bank_cash_name' => $BankCash->name,
                    'opening_balance' => $openingBalance,
                    'type' => $type,
                    'from' => $from,
                    'to' => $to,
                ];
            }
        }
        // Fetch shipments from the second database where payment_method matches the bank cash name
        $shipmentsQuery = DB::connection('mysql2')
            ->table('shipments')
            ->join('branches', 'shipments.branch_id', '=', 'branches.id')
            ->join('customers', 'shipments.sender_id', '=', 'customers.id')
            ->select('shipments.*', 'branches.name', 'customers.name as sender')
            ->where('shipments.payment_method', $bank_cash_name);

        // Add the branch name condition only if it's not null or empty
        if (!empty($branch_name)) {
            $shipmentsQuery->where('branches.name', $branch_name);
        }

        // Add the date range filter only if `$from` and `$to` are provided
        if (!empty($from) && !empty($to)) {
            $shipmentsQuery->whereBetween('shipments.created_at', [$from, $to]);
        }

        // Execute the query and get the results
        $shipments = $shipmentsQuery->get();
        // Fetch transactions within the specified date range (from - to)
        $bank_cashes_balance = Helper::__bank_cash_details($request->bank_cash_id, $request->branch_id, $from, $to, $shipments)
            ->sortBy('voucher_date'); // Sort the collection

        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.ledger.bank-cash-wise.index')
                ->with('items', $bank_cashes_balance)
                ->with('shipments', $shipments)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.ledger.bank-cash-wise.pdf', [
                'items' => $bank_cashes_balance,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $BankCashWise = new BankCashWise([
                'items' => $bank_cashes_balance,

                'extra' => $extra,
            ]);
            return Excel::download($BankCashWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }



    /*
    public function ledger_bank_cash_wise_report(Request $request)
{
    $now = new \DateTime();
    $date = $now->format(Config('settings.date_format') . ' h:i:s');
    $extra = [
        'current_date_time' => $date,
        'module_name' => 'Bank Cash Wise',
        'voucher_type' => 'BANK CASH WISE LEDGER REPORT'
    ];

    // branches from cache
    $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
    $bank_cashes = Helper::__getBranchBankCashIncomeExpenseHead()['bank_cashes'];

    // Fetch branch and bank cash names with null checks
    $branch_name = '';
    $bank_cash_name = '';

    if ($request->branch_id) {
        $branch = $branches->where('id', $request->branch_id)->first();
        if ($branch) {
            $branch_name = $branch->name;
        }
    }

    if ($request->bank_cash_id) {
        $bank_cash = $bank_cashes->where('id', $request->bank_cash_id)->first();
        if ($bank_cash) {
            $bank_cash_name = $bank_cash->name;
        }
    }

    $search_by = [
        'branch_name' => $branch_name,
        'bank_cash_name' => $bank_cash_name,
        'opening_balance' => 0,
            'type' => '',
        'from' => ($request->from) ? date(config('settings.date_format'), strtotime($request->from)) : '',
        'to' => ($request->to) ? date(config('settings.date_format'), strtotime($request->to)) : '',
    ];

    $bank_caches_balance = Helper::__bank_cash_details($request->bank_cash_id, $request->branch_id, $request->from, $request->to);

    // Show Action
    if ($request->action == 'Show') {
        return view('admin.accounts-report.ledger.bank-cash-wise.index')
            ->with('items', $bank_caches_balance)
            ->with('extra', $extra)
            ->with('search_by', $search_by);
    }

    // Pdf Action
    if ($request->action == 'Pdf') {
        $pdf = PDF::loadView('admin.accounts-report.ledger.bank-cash-wise.pdf', [
            'items' => $bank_caches_balance,
            'extra' => $extra,
            'search_by' => $search_by,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
    }

    // Excel Action
    if ($request->action == 'Excel') {
        $BankCashWise = new BankCashWise([
            'items' => $bank_caches_balance,
            'extra' => $extra,
        ]);
        return Excel::download($BankCashWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
    }
}
*/
    #end

    public function getBankCashBalance($unique_branches, $start_from, $start_to, $end_from, $end_to)
    {

        $TransactionController = new TransactionController();
        $unique_bank_cashes = $TransactionController->getUniqueBankCashes(0);
        $TransactionModel = new Transaction();
        $start_balance = 0;
        $end_balance = 0;
        $bankCashesBalanceStart = array();
        $bankCashesBalanceEnd = array();
        foreach ($unique_branches as $branch) {
            foreach ($unique_bank_cashes as $unique_bank_cash) {
                $start_balance += $startBalance = $TransactionModel->GetBankCashBalanceByBranchBankCashIdDate($branch->branch_id, $unique_bank_cash->bank_cash_id, $start_from, $start_to);
                $end_balance += $endBalance = $TransactionModel->GetBankCashBalanceByBranchBankCashIdDate($branch->branch_id, $unique_bank_cash->bank_cash_id, $end_from, $end_to);
                if (array_key_exists($unique_bank_cash->name, $bankCashesBalanceStart)) {
                    $bankCashesBalanceStart[$unique_bank_cash->name] += $startBalance;
                } else {
                    $bankCashesBalanceStart[$unique_bank_cash->name] = $startBalance;
                }
                if (array_key_exists($unique_bank_cash->name, $bankCashesBalanceEnd)) {
                    $bankCashesBalanceEnd[$unique_bank_cash->name] += $endBalance;
                } else {
                    $bankCashesBalanceEnd[$unique_bank_cash->name] = $endBalance;
                }
            }
        }
        $balance = array(
            'balance' => array(
                'start_balance' => $start_balance,
                'end_balance' => $end_balance
            ),
            'BankCashDetails' => array(
                'StartDate' => $bankCashesBalanceStart,
                'EndDate' => $bankCashesBalanceEnd,
                'TotalBalance' => array(
                    'start_balance' => $start_balance,
                    'end_balance' => $end_balance
                ),
            )
        );
        return $balance;
    }
    public function __getBranchBankCashIncomeExpenseHead()
    {
        # code... 
        $data = [];
        if (Cache::get('branches') && Cache::get('branches') != null) {
            $data['branches'] = Cache::get('branches');
        } else {
            $data['branches'] = Branch::all();
            Cache::put('branches', $data['branches']);
        }
        if (!Cache::has('bank_cashes')) {
            $data['bank_cashes'] = BankCash::whereNull('deleted_by')->get();
            Cache::put('bank_cashes', $data['bank_cashes']);
        } else {
            $data['bank_cashes'] = Cache::get('bank_cashes');
        }
        if (Cache::get('income_expense_heads') && Cache::get('income_expense_heads') != null) {
            $data['income_expense_heads'] = Cache::get('income_expense_heads');
        } else {
            $data['income_expense_heads'] = IncomeExpenseHead::all();
            Cache::put('income_expense_heads', $data['income_expense_heads']);
        }
        return $data;
    }
}

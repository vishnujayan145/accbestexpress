<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Exports\ProfitOrLossAccount\BranchWise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Helpers\Helper;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\IncomeExpenseHead;
use App\BankCash;
use App\Branch;
class ProfitAndLossAccountController extends Controller
{
   /* public function index()
    {
        return view('admin.accounts-report.profit-or-loss-account.index', Helper::__getBranchBankCashIncomeExpenseHead());
    }*/
    public function index(Request $request)
    {
       
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Report',
            'voucher_type' => 'PROFIT OR LOSS ACCOUNT'
        );
    // Fetch all records from the transactions table
    $transactions = \App\Transaction::with('incomeExpenseHead.incomeExpenseType')->get();
    // Group transactions by ledger type (income/expense type name)
    $groupedTransactions = $transactions->groupBy(function ($transaction) {
        return $transaction->incomeExpenseHead->incomeExpenseType->name ?? 'Unknown';
    });
    

    return view('admin.accounts-report.profit-or-loss-account.branch-wise.index')
          ->with('extra', $extra)
          ->with('groupedTransactions', $groupedTransactions)
          ->with('transactions', $transactions);
        
    }

   /* public function branch_wise(Request $request)
    {
       
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Report',
            'voucher_type' => 'PROFIT OR LOSS ACCOUNT'
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
                                       ->orderBy('voucher_date', 'asc') // Ascending order
                    ->get();
    
    
                // Update search_by array with the computed values
                $search_by['income_expense_head_name'] = $incomeExpenseHead->name;
              
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
    
       
      
        return view('admin.accounts-report.profit-or-loss-account.branch-wise.index')
        ->with('items', $income_expense_head_query->get())
        ->with('extra', $extra)
        ->with('search_by', $search_by);
    }
*/
   /* public function branch_wise(Request $request)
    {
        $request->validate([
            'end_from' => 'bail|nullable',
            'end_to' => 'bail|nullable',
            'start_from' => 'bail|nullable',
            'start_to' => 'bail|nullable',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Profit And Loss Account',
            'voucher_type' => 'PROFIT OR LOSS ACCOUNT'
        );
        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $search_by = array(
            'branch_name' => ($request->branch_id) ? $branches->where('id', $request->branch_id)->first()->name : 'All Branch',
            'start_from' => ($request->start_from) ?  date(config('settings.date_format'), strtotime($request->start_from)) : null,
            'start_to' => ($request->start_to) ?  date(config('settings.date_format'), strtotime($request->start_to)) : null,
            'end_from' => ($request->end_from) ?  date(config('settings.date_format'), strtotime($request->end_from)) : null,
            'end_to' => ($request->end_to) ?  date(config('settings.date_format'), strtotime($request->end_to)) : null,
        
        );
        $items = $this->getProfitOrLoss(
            $request->branch_id,
            $request->start_from,
            $request->start_to,
            $request->end_from,
            $request->end_to
        );
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.profit-or-loss-account.branch-wise.index')
                ->with('particulars', $items['Particulars'])
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.profit-or-loss-account.branch-wise.pdf', [
                'particulars' => $items['Particulars'],
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }
        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWise([
                'particulars' => $items['Particulars'],
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }
*/
    public function getProfitOrLoss($branch_id, $start_from, $start_to, $end_from, $end_to)
    {
        //  Profit & Loss Account
        // Code  105  ( Revenue )
        // Cost Of Revenue ( Get From Function get_cost_of_revenue )
        // Gross Profit  ( Revenue - cost of revenue )
        // code  106  (Indirect Income)
        // Income From Operation ( Gross Profit + Indirect Income )
        // code 116  ( Administration Expenses )
        // Income Before Tax & Interest ( Income From Operation - Administration Expenses )
        // code 117 (Financial Expense)
        // Income After Tax & Interest ( Income Before Tax & Interest -  Financial Expense)
        //Provision & Tax Paid 0
        // Net Profit/ (Loss) ( Income After Tax & Interest )

        $types = [102, 104, 114, 115, 105, 106, 116, 117];
        $all_types_start = Helper::totalAmountByLedgerType($types, $branch_id, $start_from, $start_to);
        $all_types_end = Helper::totalAmountByLedgerType($types, $branch_id, $end_from, $end_to);

        // cost of revenue types
        $cost_of_revenue_types = [102, 104, 114, 115];
        //Revenue 
        $revenue_code = 105;
        //Indirect income
        $indirect_income_code = 106;
        //administrative Expenses
        $administrative_expenses_code = 116;
        //Financial Expense
        $financial_expense_code = 117;

        $start_cost_of_revenue = 0;
        $end_cost_of_revenue = 0;
        foreach ($cost_of_revenue_types as $cost_of_revenue_type) {
            $start_cost_of_revenue +=  $all_types_start['items'][$cost_of_revenue_type]['value'];
            $end_cost_of_revenue += $all_types_end['items'][$cost_of_revenue_type]['value'];
        }
        $Revenue = [
            'start_balance' => $all_types_start['items'][$revenue_code]['value'],
            'end_balance' => $all_types_end['items'][$revenue_code]['value']
        ];
        $cost_of_revenue = [
            'start_balance' => $start_cost_of_revenue,
            'end_balance' => $end_cost_of_revenue
        ];
        $GrossProfit = array(
            'start_balance' => $Revenue['start_balance'] - $cost_of_revenue['start_balance'],
            'end_balance' => $Revenue['end_balance'] - $cost_of_revenue['end_balance'],
        );
        $IndirectIncome = [
            'start_balance' => $all_types_start['items'][$indirect_income_code]['value'],
            'end_balance' => $all_types_end['items'][$indirect_income_code]['value']
        ];
        $IncomeFromOperation = array(
            'start_balance' => $GrossProfit['start_balance'] + $IndirectIncome['start_balance'],
            'end_balance' => $GrossProfit['end_balance'] + $IndirectIncome['end_balance'],
        );
        $AdministrationExpenses = [
            'start_balance' => $all_types_start['items'][$administrative_expenses_code]['value'],
            'end_balance' => $all_types_end['items'][$administrative_expenses_code]['value']
        ];
        $IncomeBeforeTaxAndInterest = array(
            'start_balance' => $IncomeFromOperation['start_balance'] - $AdministrationExpenses['start_balance'],
            'end_balance' => $IncomeFromOperation['end_balance'] - $AdministrationExpenses['end_balance'],
        );
        $FinancialExpense = [
            'start_balance' => $all_types_start['items'][$financial_expense_code]['value'],
            'end_balance' => $all_types_end['items'][$financial_expense_code]['value']
        ];
        $IncomeAfterTaxAndInterest = array(
            'start_balance' => $IncomeBeforeTaxAndInterest['start_balance'] - $FinancialExpense['start_balance'],
            'end_balance' => $IncomeBeforeTaxAndInterest['end_balance'] - $FinancialExpense['end_balance'],
        );
        $ProvisionAndTaxPaid = array(
            'start_balance' => 0,
            'end_balance' => 0,
        );
        $NetProfitOrLoss = $IncomeAfterTaxAndInterest;
        $particulars = array(
            'Revenue' => array(
                'name' => $all_types_start['items'][$revenue_code]['name'],
                'code' => $revenue_code,
                'balance' => $Revenue,
            ),
            'CostOfRevenue' => array(
                'name' => 'Cost Of Revenue',
                'code' => 'CostOfRevenue',
                'balance' => $cost_of_revenue,
            ),
            'GrossProfit' => array(
                'name' => 'Gross Profit',
                'code' => 'GrossProfit',
                'balance' => $GrossProfit,
            ),
            'IndirectIncome' => array(
                'name' => $all_types_start['items'][$indirect_income_code]['name'],
                'code' => $indirect_income_code,
                'balance' => $IndirectIncome,
            ),
            'IncomeFromOperation' => array(
                'name' => 'Income From Operation',
                'code' => 'IncomeFromOperation',
                'balance' => $IncomeFromOperation,
            ),
            'AdministrationExpenses' => array(
                'name' => $all_types_start['items'][$administrative_expenses_code]['name'],
                'code' => $administrative_expenses_code,
                'balance' => $AdministrationExpenses,
            ),
            'IncomeBeforeTaxAndInterest' => array(
                'name' => 'Income Before Tax And Interest',
                'code' => 'IncomeBeforeTaxAndInterest',
                'balance' => $IncomeBeforeTaxAndInterest,
            ),
            'FinancialExpense' => array(
                'name' => $all_types_start['items'][$financial_expense_code]['name'],
                'code' => $financial_expense_code,
                'balance' => $FinancialExpense,
            ),
            'IncomeAfterTaxAndInterest' => array(
                'name' => 'Income After Tax And Interest',
                'code' => 'IncomeAfterTaxAndInterest',
                'balance' => $IncomeAfterTaxAndInterest,
            ),
            'ProvisionAndTaxPaid' => array(
                'name' => 'Provision And Tax Paid',
                'code' => 'ProvisionAndTaxPaid',
                'balance' => $ProvisionAndTaxPaid,
            ),
            'NetProfitOrLoss' => array(
                'name' => 'Net Profit/ (Loss)',
                'code' => 'ProvisionAndTaxPaid',
                'balance' => $NetProfitOrLoss,
            ),
        );
        $data = array(
            'Particulars' => $particulars,
            'NetProfitOrLoss' => $NetProfitOrLoss,
        );
        return $data;
    }
}

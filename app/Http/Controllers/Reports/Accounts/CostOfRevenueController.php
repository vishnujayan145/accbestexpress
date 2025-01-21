<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Branch;
use App\Transaction;

use App\IncomeExpenseType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Exports\CostOfRevenue\BranchWise;
use App\Helpers\Helper;
use App\Http\Controllers\CrVoucherController;

class CostOfRevenueController extends Controller
{
    public function index()
    {
        $crvoucher = new CrVoucherController();
        return view('admin.accounts-report.cost-of-revenue.index', $crvoucher->__getBranchBankCashIncomeExpenseHead());
    }
    public function branch_wise(Request $request)
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
            'module_name' => 'Cost Of Revenue',
            'voucher_type' => 'COST OF REVENUE'
        );
        // Type Cost Of Revenue
        // Code  102 ( Construction Material Purchases)
        // Code  104 ( Construction Labour Expenses)
        // Code  114 ( Project Approval Expense)
        // Code  115 ( Other Expense )
        //
        $code = [102, 104, 114, 115];
        $from_amounts = Helper::totalAmountByLedgerType($code, $request->branch_id, $request->start_from, $request->start_to);
        $end_amounts = Helper::totalAmountByLedgerType($code, $request->branch_id, $request->end_from, $request->end_to);
        $from_total = 0;
        foreach ($from_amounts['items'] as $from_amount) {
            $from_total += $from_amount['value'];
        }
        $end_total = 0;
        foreach ($end_amounts['items'] as $end_amount) {
            $end_total += $end_amount['value'];
        }
        $particulars = [
            'OpeningConstructionMaterial' => 'Opening Construction Material',
            'ConstructionMaterialPurchases' => [
                'from_amount' => $from_amounts['items'][$code[0]],
                'end_amount' => $end_amounts['items'][$code[0]],
            ],
            'MaterialAvailableForUsed' => 'Material Available For Used',
            'ClosingConstructionMaterial' => 'Closing Construction Material',
            'MaterialUsedDuringThePeriod' => 'Material Used During the Period',
            'ConstructionLabourExpense' => [
                'from_amount' => $from_amounts['items'][$code[1]],
                'end_amount' => $end_amounts['items'][$code[1]],
            ],
            'ProjectApprovalExpenses' => [
                'from_amount' => $from_amounts['items'][$code[2]],
                'end_amount' => $end_amounts['items'][$code[2]],
            ],
            'OtherExpense' => [
                'from_amount' => $from_amounts['items'][$code[3]],
                'end_amount' => $end_amounts['items'][$code[3]],
            ],
            'TotalCostTransferredToWorkInProcess' => 'Total Cost Transferred to Work in Process',
            'OpeningWorkInProcess' => 'Opening Work in Process',
            'ClosingWorkInProcess' => 'Closing Work in Process',
            'CostOfRevenue' => [
                'from_total' => $from_total,
                'end_total' => $end_total,
            ],
        ];
        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $search_by = array(
            'branch_name' => ($request->branch_id) ? $branches->where('id', $request->branch_id)->first()->name : 'All Branch',
            'start_from' => ($request->start_from) ?  date(config('settings.date_format'), strtotime($request->start_from)) : null,
            'start_to' => ($request->start_to) ?  date(config('settings.date_format'), strtotime($request->start_to)) : null,
            'end_from' => ($request->end_from) ?  date(config('settings.date_format'), strtotime($request->end_from)) : null,
            'end_to' => ($request->end_to) ?  date(config('settings.date_format'), strtotime($request->end_to)) : null,
        );
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.cost-of-revenue.branch-wise.index')
                ->with('particulars', $particulars)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.cost-of-revenue.branch-wise.pdf', [
                'particulars' => $particulars,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }
        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWise([
                'particulars' => $particulars,
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }

    public function get_cost_of_revenue($transaction_unique_branches, $start_from, $start_to, $end_from, $end_to)
    {

        //  Income Expense
        //
        // Type Cost Of Revenue
        // Code  102 ( Construction Material Purchases)
        // Code  104 ( Construction Labour Expenses)
        // Code  114 ( Project Approval Expense)
        // Code  115 ( Other Expense )
        //

        $CostOfRevenueHeadTypes = IncomeExpenseType::whereIn('code', array(102, 104, 114, 115))
            ->orderBy('code', 'asc')
            ->get();

        $Transactions = new Transaction();

        foreach ($CostOfRevenueHeadTypes as $costOfRevenueHeadType) {
            $Balances[$costOfRevenueHeadType->code] = $Transactions->getBalanceByIncExpHeadTypeIdBranchesTwoDate(
                $costOfRevenueHeadType->id,
                $transaction_unique_branches,
                $start_from,
                $start_to,
                $end_from,
                $end_to
            );
        }
        $total_start_balance = 0;
        $total_end_balance = 0;
        foreach ($Balances as $balance) {
            $total_start_balance += $balance['balance']['start_balance'];
            $total_end_balance += $balance['balance']['end_balance'];
        }
        $cost_of_revenue = array(
            'start_balance' => $total_start_balance,
            'end_balance' => $total_end_balance,
        );
        return $cost_of_revenue;
    }
}

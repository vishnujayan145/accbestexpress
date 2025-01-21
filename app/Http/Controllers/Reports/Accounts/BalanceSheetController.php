<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Balancesheet\BalanceShet;

class BalanceSheetController extends Controller
{
    public function index()
    {
        return view('admin.accounts-report.balance-sheet.index', Helper::__getBranchBankCashIncomeExpenseHead());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function branch_wise(Request $request)
    {
        $request->validate([
            'end_from' => 'required',
            'end_to' => 'required',
            'start_from' => 'required',
            'start_to' => 'required',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Balance Sheet',
            'voucher_type' => 'STATEMENT OF FINANCIAL POSITION'
        );
        $BalanceSheet = $this->getBalanceSheet(
            $request->branch_id,
            $request->start_from,
            $request->start_to,
            $request->end_from,
            $request->end_to
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
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.balance-sheet.branch-wise.index')
                ->with('particulars', $BalanceSheet)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }

        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.profit-or-loss-account.branch-wise.pdf', [
                'particulars' => $BalanceSheet,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');

            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BalanceShet([
                'particulars' => $BalanceSheet,
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    public function getBalanceSheet($branch_id, $start_from, $start_to, $end_from, $end_to)
    {
        //   CAPITAL & LIABILITIES
        // AUTHORIZED CAPITAL Default ( let 100000000 )
        // PAID UP CAPITAL Code (  108  )
        // RETAIN EARNING ( From Retained Earnings Module )
        // SHARE MONEY DEPOSIT Code ( 113 )
        // NON-CURRENT LIABILITIES = ( Long Term Loan )
        // Long Term Loan Code ( 109 )
        // CURRENT LIABILITIES = ( Account Payable + Short Term Loan + Advance Against Sales + Other Payable + Advance Receive from Investor )
        // Account Payable Code ( 107 )
        // Short Term Loan Code ( 110 )
        // Advance Against Sales Code ( 112 )
        // Other Payable Code ( 111 )
        // Advance Receive from Investor Code ( 120 )
        // Total ( AUTHORIZED CAPITAL + PAID UP CAPITAL + RETAIN EARNING + SHARE MONEY DEPOSIT + NON-CURRENT LIABILITIES + CURRENT LIABILITIES: )
        // ASSETS
        // NON-CURRENT ASSETS: ( Property, Plant & Equipment )
        // Property, Plant & Equipment From ( Fixed asset Schedule function )
        // CURRENT ASSETS: ( Advance, Deposit & Receivables + Inventories + Cash & Bank Balance)
        // Advance, Deposit & Receivables Code ( 103 )
        // Inventories Code ( 101 )
        // Cash & Bank Balance ( From Bank Cash Function )
        // Preliminary Expense Code ( 121 )
        // Total ( NON-CURRENT ASSETS + CURRENT ASSETS + Preliminary Expense )
        // Unique Branches or single
        $balance_sheet_types = config('accounts_config.balance_sheet');
        $all_types_start = Helper::totalAmountByLedgerType($balance_sheet_types, $branch_id, $start_from, $start_to);
        $all_types_end = Helper::totalAmountByLedgerType($balance_sheet_types, $branch_id, $end_from, $end_to);

        $RetainedEarningsController = new  RetainedEarningsController();
        $RetainedEarnings = $RetainedEarningsController->getRetainedEarnings($branch_id, $start_from, $start_to, $end_from, $end_to);
        $fixedAssetsController = new FixedAssetScheduleController();
        $fixedAssetsSchedule = $fixedAssetsController->getFixedAssetSchedule($branch_id, $start_from, $end_from);

        // bank cache start end balance
        $bank_cache_balance_start = Helper::__bank_cache_balance(null, $branch_id, $start_from, $start_to);
        $bank_cache_balance_end = Helper::__bank_cache_balance(null, $branch_id, $end_from, $end_to);


        $CapitalAndLiabilitiesBalance = [
            'start_balance' => 0,
            'end_balance' => 0,
        ];
        $AuthorizedCapitalBalance = [
            'start_balance' => 0,
            'end_balance' => 0,
        ];
        $IssuedSubscribedAndPaidUpCapitalBalance = [
            'start_balance' => $all_types_start['items'][108]['value'],
            'end_balance' => $all_types_end['items'][108]['value'],
        ];
        $ShareMoneyDepositBalance = [
            'start_balance' => $all_types_start['items'][113]['value'],
            'end_balance' => $all_types_end['items'][113]['value'],
        ];
        $NonCurrentLiabilitiesBalance = [
            'start_balance' => $all_types_start['items'][109]['value'],
            'end_balance' => $all_types_end['items'][109]['value'],
        ];
        $LongTermLoanBalance = [
            'start_balance' => $all_types_start['items'][109]['value'],
            'end_balance' => $all_types_end['items'][109]['value'],
        ];
        $CurrentLiabilitiesBalance = [
            'start_balance' => $all_types_start['items'][107]['value'] + $all_types_start['items'][110]['value'] + $all_types_start['items'][112]['value'] + $all_types_start['items'][111]['value'] + $all_types_start['items'][120]['value'],
            'end_balance' => $all_types_end['items'][107]['value'] + $all_types_end['items'][110]['value'] + $all_types_end['items'][112]['value'] + $all_types_end['items'][111]['value'] + $all_types_end['items'][120]['value'],
        ];
        $AccountPayableBalance = [
            'start_balance' => $all_types_start['items'][107]['value'],
            'end_balance' => $all_types_end['items'][107]['value'],
        ];
        $ShortTermLoanBalance = [
            'start_balance' => $all_types_start['items'][110]['value'],
            'end_balance' => $all_types_end['items'][110]['value'],
        ];
        $AdvanceAgainstSalesBalance = [
            'start_balance' => $all_types_start['items'][112]['value'],
            'end_balance' => $all_types_end['items'][112]['value'],
        ];
        $OtherPayableBalance = [
            'start_balance' => $all_types_start['items'][111]['value'],
            'end_balance' => $all_types_end['items'][111]['value'],
        ];
        $AdvanceReceiveFromInvestorBalance = [
            'start_balance' => $all_types_start['items'][120]['value'],
            'end_balance' => $all_types_end['items'][120]['value'],
        ];
        $TotalCapitalAndLiabilitiesBalance = [
            'start_balance' => $AuthorizedCapitalBalance['start_balance'] + $IssuedSubscribedAndPaidUpCapitalBalance['start_balance'] + $RetainedEarnings['NetProfitOrLoss']['start_balance'] + $ShareMoneyDepositBalance['start_balance'] + $NonCurrentLiabilitiesBalance['start_balance'] + $CurrentLiabilitiesBalance['start_balance'],
            'end_balance' => $AuthorizedCapitalBalance['end_balance'] + $IssuedSubscribedAndPaidUpCapitalBalance['end_balance'] + $RetainedEarnings['NetProfitOrLoss']['end_balance'] + $ShareMoneyDepositBalance['end_balance'] + $NonCurrentLiabilitiesBalance['end_balance'] + $CurrentLiabilitiesBalance['end_balance'],
        ];
        $AssetsBalance = [
            'start_balance' => 0,
            'end_balance' => 0,
        ];
        $NonCurrentAssetsBalance = $fixedAssetsSchedule['TotalBalance'];
        $Current_Assets = [
            'start_balance' => $all_types_start['items'][103]['value'] + $all_types_start['items'][101]['value'] + $bank_cache_balance_start,
            'end_balance' => $all_types_end['items'][103]['value'] + $all_types_end['items'][101]['value'] + $bank_cache_balance_end,
        ];
        $AdvanceDepositReceivables = [
            'start_balance' => $all_types_start['items'][103]['value'],
            'end_balance' => $all_types_end['items'][103]['value'],
        ];
        $InventoriesBalance = [
            'start_balance' => $all_types_start['items'][101]['value'],
            'end_balance' => $all_types_end['items'][101]['value'],
        ];
        $CashAndBankBalance = [
            'start_balance' => $bank_cache_balance_start,
            'end_balance' => $bank_cache_balance_end,
        ];
        $PreliminaryExpenseBalance = [
            'start_balance' => $all_types_start['items'][121]['value'],
            'end_balance' => $all_types_end['items'][121]['value'],
        ];
        $TotalAssetsBalance = [
            'start_balance' => $NonCurrentAssetsBalance['start_balance'] + $Current_Assets['start_balance'] + $PreliminaryExpenseBalance['start_balance'],
            'end_balance' => $NonCurrentAssetsBalance['end_balance'] + $Current_Assets['end_balance'] + $PreliminaryExpenseBalance['end_balance'],
        ];

        $particulars = [
            'CapitalAndLiabilities' => [
                'name' => 'CAPITAL AND LIABILITIES',
                'code' => 'particulars',
                'balance' => $CapitalAndLiabilitiesBalance,
            ],
            'AuthorizedCapital' => [
                'name' => 'AUTHORIZED CAPITAL',
                'code' => 'AuthorizedCapital',
                'balance' => $AuthorizedCapitalBalance,
            ],
            'IssuedSubscribedAndPaidUpCapital' => [
                'name' => $all_types_end['items'][108]['name'],
                'code' => 108,
                'balance' => $IssuedSubscribedAndPaidUpCapitalBalance,
            ],
            'RetainEarning' => [
                'name' => 'RETAIN EARNING',
                'code' => 'RetainEarning',
                'balance' => $RetainedEarnings['NetProfitOrLoss'],
            ],
            'ShareMoneyDeposit' => [
                'name' => $all_types_end['items'][113]['name'],
                'code' => '113',
                'balance' => $ShareMoneyDepositBalance,
            ],
            'NonCurrentLiabilities' => [
                'name' => 'NON-CURRENT LIABILITIES:',
                'code' => 'NonCurrentLiabilities',
                'balance' => $NonCurrentLiabilitiesBalance,
            ],
            'LongTermLoan' => [
                'name' => $all_types_end['items'][109]['name'],
                'code' => 109,
                'balance' => $LongTermLoanBalance,
            ],

            'CurrentLiabilities' => [
                'name' => 'CURRENT LIABILITIES:',
                'code' => 'CurrentLiabilities',
                'balance' => $CurrentLiabilitiesBalance,
            ],
            'AccountPayable' => [
                'name' => $all_types_end['items'][107]['name'],
                'code' => 107,
                'balance' => $AccountPayableBalance,
            ],
            'ShortTermLoan' => [
                'name' => $all_types_end['items'][110]['name'],
                'code' => 110,
                'balance' => $ShortTermLoanBalance,
            ],
            'AdvanceAgainstSales' => [
                'name' => $all_types_end['items'][112]['name'],
                'code' => 112,
                'balance' => $AdvanceAgainstSalesBalance,
            ],
            'OtherPayable' => [
                'name' => $all_types_end['items'][111]['name'],
                'code' => 111,
                'balance' => $OtherPayableBalance,
            ],
            'AdvanceReceiveFromInvestor' => [
                'name' => $all_types_end['items'][120]['name'],
                'code' => 120,
                'balance' => $AdvanceReceiveFromInvestorBalance,
            ],
            'TotalCapitalAndLiabilities' => [
                'name' => 'Total =',
                'code' => 'TotalCapitalAndLiabilities',
                'balance' => $TotalCapitalAndLiabilitiesBalance,
            ],

            'Assets' => [
                'name' => 'ASSETS',
                'code' => 'Assets',
                'balance' => $AssetsBalance,
            ],
            'NonCurrentAssets' => [
                'name' => 'NON-CURRENT ASSETS:',
                'code' => 'NonCurrentAssets:',
                'balance' => $NonCurrentAssetsBalance,
            ],
            'fixedAssetsSchedule' => [
                'name' => 'Property, Plant And Equipment',
                'code' => 'fixedAssetsSchedule',
                'balance' => $fixedAssetsSchedule['TotalBalance'],
            ],
            'CurrentAssets' => [
                'name' => 'CURRENT ASSETS:',
                'code' => 'CurrentAssets',
                'balance' => $Current_Assets,
            ],
            'AdvanceDepositReceivables' => [
                'name' => $all_types_end['items'][103]['name'],
                'code' => 103,
                'balance' => $AdvanceDepositReceivables,
            ],
            'Inventories' => [
                'name' => $all_types_end['items'][101]['name'],
                'code' => 101,
                'balance' => $InventoriesBalance,
            ],
            'CashAndBankBalance' => [
                'name' => 'Cash And Bank Balance',
                'code' => 'CashAndBankBalance',
                'balance' => $CashAndBankBalance,
            ],
            'PreliminaryExpense' => [
                'name' => $all_types_end['items'][121]['name'],
                'code' => 121,
                'balance' => $PreliminaryExpenseBalance,
            ],
            'TotalAssets' => [
                'name' => 'Total =',
                'code' => 'TotalAssets',
                'balance' => $TotalAssetsBalance,
            ],

        ];
        return $particulars;
    }
}

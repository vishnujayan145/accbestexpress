<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Exports\RetainedEarnings\BranchWise;
use App\Http\Controllers\TransactionController;
use App\IncomeExpenseType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Branch;
use App\Helpers\Helper;
use App\Transaction;

use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

class RetainedEarningsController extends Controller
{
    public function index()
    {
        $data = Helper::__getBranchBankCashIncomeExpenseHead();
        return view('admin.accounts-report.retained-earnings.index', $data);
    }

    public function branch_wise(Request $request)
    {
        $request->validate([
            'start_from' => 'required',
            'start_to' => 'required',
            'end_from' => 'required',
            'end_to' => 'required',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Retained Earnings Branch',
            'voucher_type' => 'RETAINED EARNINGS'
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
        $RetainedEarnings = $this->getRetainedEarnings($request->branch_id, $request->start_from, $request->start_to, $request->end_from, $request->end_to);
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.retained-earnings.branch-wise.index')
                ->with('particulars', $RetainedEarnings['Particulars'])
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.retained-earnings.branch-wise.pdf', [
                'particulars' => $RetainedEarnings['Particulars'],
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }

        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWise([
                'particulars' => $RetainedEarnings['Particulars'],
                'extra' => $extra,
                'search_by' => $search_by,
            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    public function getRetainedEarnings($branch_id, $start_from, $start_to, $end_from, $end_to)
    {
        //  Retained Earnings
        //  Net Profit And Loss Previous Year ( Right hand Start Company date  To $end_from date
        //  Left Hand Net Profit and loss Right side
        // )
        //  Net Profit And Loss During The Year ( getProfitOrLoss Function )
        //  Dividend ( Balance from IncomeExpenseHeadType code 122 )
        //  Net Profit and (Loss)  ( Net Profit And Loss Previous Year + Net Profit And Loss During The Year - Dividend)
        
        $CompanyStartingDate = config('settings.fixed_asset_schedule_starting_date');
        $date_start_from = new \DateTime($start_from);
        $start_from_date_minus_12_modify = $date_start_from->modify("-12 months"); // Last day 12 months ago
        $start_from_minus_12 = $start_from_date_minus_12_modify->format('Y-m-d');

        $date_end_from = new \DateTime($end_from);
        $end_from_date_minus_12_modify = $date_end_from->modify("-12 months"); // Last day 12 months ago
        $end_from_minus_12 = $end_from_date_minus_12_modify->format('Y-m-d');

        $ProfitOrLoss = new ProfitAndLossAccountController();
        // Net Profit And Loss Previous Year Right side
        $NetProfitAndLossPreviousYearRight = $ProfitOrLoss->getProfitOrLoss(
            $branch_id,
            $CompanyStartingDate,
            $end_from,
            $CompanyStartingDate,
            $end_from
        );
        // Net Profit And Loss During The Year
        $NetProfitAndLossDuringTheYear = $ProfitOrLoss->getProfitOrLoss(
            $branch_id,
            $start_from,
            $start_to,
            $end_from,
            $end_to
        );
        // Dividend  IncomeExpenseHeadType Code 122
        $types = [122];
        $all_types_start = Helper::totalAmountByLedgerType($types, $branch_id, $start_from, $start_to);
        $all_types_end = Helper::totalAmountByLedgerType($types, $branch_id, $end_from, $end_to);
        
        $DividendBalance = [
            'start_balance' => $all_types_start['items'][$types[0]]['value'],
            'end_balance' => $all_types_end['items'][$types[0]]['value'],
        ];
        $NetProfitOrLossPreviousYearRightSideBalance = $NetProfitAndLossPreviousYearRight['NetProfitOrLoss']['end_balance'];
        $rightSideProfitAndLoss = $NetProfitOrLossPreviousYearRightSideBalance + $NetProfitAndLossDuringTheYear['NetProfitOrLoss']['end_balance'] - $DividendBalance['end_balance'];
        $NetProfitOrLoss = [
            'start_balance' => ($rightSideProfitAndLoss + $NetProfitAndLossDuringTheYear['NetProfitOrLoss']['start_balance'] - $DividendBalance['start_balance']),
            'end_balance' => $rightSideProfitAndLoss,
        ];
        $particulars = [
            'NetProfitAndLossPreviousYear' => [
                'name' => 'Net Profit And Loss Previous Year',
                'code' => 'NetProfitAndLossPreviousYear',
                'balance' => [
                    'start_balance' => $NetProfitOrLoss['end_balance'],
                    'end_balance' => $NetProfitOrLossPreviousYearRightSideBalance
                ],
            ],
            'NetProfitAndLossDuringTheYear' => [
                'name' => 'Net Profit And Loss During The Year',
                'code' => 'NetProfitAndLossDuringTheYear',
                'balance' => $NetProfitAndLossDuringTheYear['NetProfitOrLoss'],
            ],
            'DividendPaid' => [
                'name' => $all_types_start['items'][$types[0]]['name'],
                'code' => [$types[0]],
                'balance' => $DividendBalance,
            ],
            'NetProfitOrLoss' => [
                'name' => 'Net Profit And (Loss)',
                'code' => 'NetProfitLoss',
                'balance' => $NetProfitOrLoss,
            ],
        ];
        $data = [
            'Particulars' => $particulars,
            'NetProfitOrLoss' => $NetProfitOrLoss,
        ];
        return $data;
    }
}

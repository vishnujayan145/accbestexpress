<?php

namespace App\Http\Controllers\Reports\Accounts;

use App\Branch;
use App\Transaction;
use App\Helpers\Helper;
use App\IncomeExpenseType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FixedAssetSchedule\BranchWise;
use App\Http\Controllers\TransactionController;

class FixedAssetScheduleController extends Controller
{
    public function index()
    {
        return view('admin.accounts-report.fixed-asset-schedule.index', Helper::__getBranchBankCashIncomeExpenseHead());
    }
    public function branch_wise(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
        ]);
        $now = new \DateTime();
        $date = $now->format(Config('settings.date_format') . ' h:i:s');
        $extra = array(
            'current_date_time' => $date,
            'module_name' => 'Fixed Assets Schedule',
            'voucher_type' => 'FIXED ASSETS SCHEDULE'
        );
        // branches from cache
        $branches = Helper::__getBranchBankCashIncomeExpenseHead()['branches'];
        $search_by = array(
            'branch_name' => ($request->branch_id) ? $branches->where('id', $request->branch_id)->first()->name : 'All Branch',
            'branch_id' => $request->branch_id,
            'from' => date(config('settings.date_format'), strtotime($request->from)),
            'to' => date(config('settings.date_format'), strtotime($request->to)),
        );
        $FixedAssetSchedule = $this->getFixedAssetSchedule($request->branch_id, $request->from, $request->to);
        // Show Action
        if ($request->action == 'Show') {
            return view('admin.accounts-report.fixed-asset-schedule.branch-wise.index')
                ->with('particulars', $FixedAssetSchedule)
                ->with('extra', $extra)
                ->with('search_by', $search_by);
        }
        // Pdf Action
        if ($request->action == 'Pdf') {
            $pdf = PDF::loadView('admin.accounts-report.fixed-asset-schedule.branch-wise.pdf', [
                'particulars' => $FixedAssetSchedule,
                'extra' => $extra,
                'search_by' => $search_by,
            ])->setPaper('a4', 'landscape');
            //return $pdf->stream(date(config('settings.date_format'), strtotime($extra['current_date_time'])) . '_' . $extra['module_name'] . '.pdf');
            return $pdf->download($extra['current_date_time'] . '_' . $extra['module_name'] . '.pdf');
        }
        // Excel Action
        if ($request->action == 'Excel') {
            $BranchWise = new BranchWise([
                'particulars' => $FixedAssetSchedule,
                'extra' => $extra,
                'search_by' => $search_by,

            ]);
            return Excel::download($BranchWise, $extra['current_date_time'] . '_' . $extra['module_name'] . '.xlsx');
        }
    }


    public function getFixedAssetSchedule($branch_id, $from, $to)
    {

        // Get Fixed Asset Schedule Starting date from global varibable setting system
        $FixedAssetScheduleStartingDate = date('Y-m-d', strtotime(config('settings.fixed_asset_schedule_starting_date')));

        // Property, Plant & Equipment Code 119
        $TransactionsModel = new Transaction();
        $TransactionsController = new TransactionController();
        $uniqueBranches = $TransactionsController->getUniqueBranches($branch_id);
        $CostOfRevenueHeadTypes = IncomeExpenseType::whereIn('code', array(119))
            ->orderBy('code', 'asc')
            ->get();

        foreach ($CostOfRevenueHeadTypes as $costOfRevenueHeadType) {
            $FixedAssetDetails[$costOfRevenueHeadType->code] = $TransactionsModel->getBalanceByIncExpHeadTypeIdBranchesTwoDate($costOfRevenueHeadType->id, $uniqueBranches, $FixedAssetScheduleStartingDate, $from, $FixedAssetScheduleStartingDate, $to);
        }
        return $FixedAssetDetails[119]['headDetails'];
    }
}

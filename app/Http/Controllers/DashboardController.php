<?php

namespace App\Http\Controllers;

use App\User;
use App\Branch;
use App\BankCash;
use App\RoleManage;
use App\Transaction;
use App\IncomeExpenseHead;
use App\IncomeExpenseType;
use App\IncomeExpenseGroup;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        if (Cache::get('total_branches') && Cache::get('total_branches') != null) {
            $data['total_branches'] = Cache::get('total_branches');
        } else {
            $data['total_branches'] = Branch::count();
            Cache::put('total_branches', $data['total_branches']);
        }

        if (Cache::get('total_income_expense_types') && Cache::get('total_income_expense_types') != null) {
            $data['total_income_expense_types'] = Cache::get('total_income_expense_types');
        } else {
            $data['total_income_expense_types'] = IncomeExpenseType::count();
            Cache::put('total_income_expense_types', $data['total_income_expense_types']);
        }

        if (Cache::get('total_income_expense_groups') && Cache::get('total_income_expense_groups') != null) {
            $data['total_income_expense_groups'] = Cache::get('total_income_expense_groups');
        } else {
            $data['total_income_expense_groups'] = IncomeExpenseGroup::count();
            Cache::put('total_income_expense_groups', $data['total_income_expense_groups']);
        }

        if (Cache::get('total_income_expense_heads') && Cache::get('total_income_expense_heads') != null) {
            $data['total_income_expense_heads'] = Cache::get('total_income_expense_heads');
        } else {
            $data['total_income_expense_heads'] = IncomeExpenseHead::count();
            Cache::put('total_income_expense_heads', $data['total_income_expense_heads']);
        }

        if (Cache::get('total_bank_cashes') && Cache::get('total_bank_cashes') != null) {
            $data['total_bank_cashes'] = Cache::get('total_bank_cashes');
        } else {
            $data['total_bank_cashes'] = BankCash::count();
            Cache::put('total_bank_cashes', $data['total_bank_cashes']);
        }

        if (Cache::get('total_users') && Cache::get('total_users') != null) {
            $data['total_users'] = Cache::get('total_users');
        } else {
            $data['total_users'] = User::count();
            Cache::put('total_users', $data['total_users']);
        }

        if (Cache::get('total_role_manages') && Cache::get('total_role_manages') != null) {
            $data['total_role_manages'] = Cache::get('total_role_manages');
        } else {
            $data['total_role_manages'] = RoleManage::count();
            Cache::put('total_role_manages', $data['total_role_manages']);
        }
        $year_start = (int) env('YEAR_START', 2016);
        $year_end = (int) env('YEAR_END', 2025);
        $years = [];
        for ($year_end; $year_end >= $year_start; $year_end--) {
            $years[$year_end] = $year_end;
        }
        $data['years'] = $years;
        return view('admin.dashboard.index', $data);
    }
    /**
     * This function retrun graph data for total voucher on dashboard
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/02/2022
     * Time         14:40:07
     * @param       $year
     * @return      
     */
    public function graphTotalVoucher($year = null)
    {
        # code...   
        // dr voucher, cr voucher, cnt voucher & journal voucher
        $total_dr_voucher = [];
        $total_cr_voucher = [];
        $total_cnt_voucher = [];
        $total_jnl_voucher = [];
        if ($year == null) {
            $year = date('Y');
        }
        $transaction_query = Transaction::query();
        $transaction_query->whereYear('voucher_date', $year);
        $transactions = $transaction_query->get();
        for ($i = 1; $i <= 12; $i++) {
            $drVoucherCount = $transactions->where('voucher_type', 'DV')->filter(function ($item, $key) use ($i) {
                $dateArray = explode('-', $item->voucher_date);
                return $i == (int) $dateArray[1];
            })->groupBy('voucher_no')->count();
            $crVoucherCount = $transactions->where('voucher_type', 'CV')->filter(function ($item, $key) use ($i) {
                $dateArray = explode('-', $item->voucher_date);
                return $i == (int) $dateArray[1];
            })->groupBy('voucher_no')->count();
            $jvVoucherCount = $transactions->where('voucher_type', 'JV')->filter(function ($item, $key) use ($i) {
                $dateArray = explode('-', $item->voucher_date);
                return $i == (int) $dateArray[1];
            })->groupBy('voucher_no')->count();
            $cntVoucherCount = $transactions->where('voucher_type', 'Contra')->filter(function ($item, $key) use ($i) {
                $dateArray = explode('-', $item->voucher_date);
                return $i == (int) $dateArray[1];
            })->groupBy('voucher_no')->count();

            array_push($total_dr_voucher, $drVoucherCount);
            array_push($total_cr_voucher, $crVoucherCount);
            array_push($total_cnt_voucher, $jvVoucherCount);
            array_push($total_jnl_voucher, $cntVoucherCount);
        }
        $data['total_dr_voucher'] = $total_dr_voucher;
        $data['total_cr_voucher'] = $total_cr_voucher;
        $data['total_cnt_voucher'] = $total_cnt_voucher;
        $data['total_jnl_voucher'] = $total_jnl_voucher;
        return response()->json($data);
    }
    #end

    /**
     * This function 
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/02/2022
     * Time         15:02:32
     * @param       
     * @return      
     */
    public function graphProfitLoss($year = null)
    {
        // types
        $ledger_types = [102, 104, 114, 115, 105, 106, 116, 117];
        $types = IncomeExpenseType::whereIn('code', $ledger_types)->select('name', 'code')->get()->toArray();
        // transaction
        $transaction_query = Transaction::query();
        $transaction_query->whereHas('IncomeExpenseHead.IncomeExpenseType', function ($query) use ($ledger_types) {
            $query->whereIn('code', $ledger_types);
        });
        $transaction_query->with('IncomeExpenseHead', 'IncomeExpenseHead.IncomeExpenseType');
        $transaction_query->whereNotNull('income_expense_head_id');
        $transactions = $transaction_query->select(\DB::raw('CONCAT(YEAR(voucher_date), "-", MONTH(voucher_date))  AS month_date'), "id", 'income_expense_head_id', 'dr', 'cr')->get()->groupBy('IncomeExpenseHead.IncomeExpenseType.code');

        $revenue = [];
        $CostOfRevenue = [];
        $GrossProfit = [];
        $IndirectIncome = [];
        $IncomeFromOperation = [];
        $AdministrationExpenses = [];
        $IncomeBeforeTaxAndInterest = [];
        $FinancialExpense = [];
        $NetProfitOrLoss = [];
        for ($i = 1; $i <= 12; $i++) {
            $current_month_year = $year . "-" . $i;
            $items = [];
            foreach ($types as $type) {
                $amount = 0;
                if ($transactions->has($type['code'])) {
                    foreach ($transactions[$type['code']] as $transaction) {
                        if ($transaction->month_date === $current_month_year) {
                            // dr
                            if ($transaction->IncomeExpenseHead->type == 1) {
                                $amount += $transaction->dr - $transaction->cr;
                            } else {
                                $amount += $transaction->cr - $transaction->dr;
                            }
                        }
                    }
                }
                $items[$type['code']] = [
                    'name' => $type['name'],
                    'value' => $amount,
                ];
            }
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

            $cost_of_revenue = 0;
            foreach ($cost_of_revenue_types as $cost_of_revenue_type) {
                $cost_of_revenue +=  $items[$cost_of_revenue_type]['value'];
            }

            $revenue_amount = $items[$revenue_code]['value'];
            $cost_of_revenue_amount = $cost_of_revenue;
            $gross_profit_amount = $revenue_amount - $cost_of_revenue;
            $indirect_income_amount = $items[$indirect_income_code]['value'];
            $income_from_operation_amount = $gross_profit_amount + $indirect_income_amount;
            $administration_expenses_amount = $items[$administrative_expenses_code]['value'];
            $income_before_tax_and_interest_amount = $income_from_operation_amount - $administration_expenses_amount;
            $financial_expense_amount = $items[$financial_expense_code]['value'];
            $income_after_tax_and_interest_amount = $income_before_tax_and_interest_amount - $financial_expense_amount;

            // push all items
            array_push($revenue, $revenue_amount);
            array_push($CostOfRevenue, $cost_of_revenue_amount);
            array_push($GrossProfit, $gross_profit_amount);
            array_push($IndirectIncome, $indirect_income_amount);
            array_push($IncomeFromOperation, $income_from_operation_amount);
            array_push($AdministrationExpenses, $administration_expenses_amount);
            array_push($IncomeBeforeTaxAndInterest, $income_before_tax_and_interest_amount);
            array_push($FinancialExpense, $financial_expense_amount);
            array_push($NetProfitOrLoss, $income_after_tax_and_interest_amount);
        }
        $data['revenue'] = $revenue;
        $data['CostOfRevenue'] = $CostOfRevenue;
        $data['GrossProfit'] = $GrossProfit;
        $data['IndirectIncome'] = $IndirectIncome;
        $data['IncomeFromOperation'] = $IncomeFromOperation;
        $data['AdministrationExpenses'] = $AdministrationExpenses;
        $data['IncomeBeforeTaxAndInterest'] = $IncomeBeforeTaxAndInterest;
        $data['FinancialExpense'] = $FinancialExpense;
        $data['NetProfitOrLoss'] = $NetProfitOrLoss;
        return $data;
    }
    #end



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('welcome');
    }
}

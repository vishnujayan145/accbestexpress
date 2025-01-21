<?php

namespace App\Helpers;

use App\Branch;
use App\BankCash;
use App\IncomeExpenseGroup;
use App\Transaction;
use App\IncomeExpenseHead;
use App\IncomeExpenseType;
use Illuminate\Support\Facades\Cache;

class Helper
{
    /**
     * This function return any number converte into words
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/19/2022
     * Time         14:31:56
     * @param       $number
     * @return      
     */
    public static function convertNumberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );
        if (!is_numeric($number)) {
            return false;
        }
        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }
        if ($number < 0) {
            return $negative . Self::convertNumberToWords(abs($number));
        }
        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Self::convertNumberToWords($remainder);
                }
                break;
        }
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        return ucfirst($string);
    }
    #end
    /**
     * This function return any number converte money format
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       08/19/2022
     * Time         14:32:59
     * @param       $number
     * @return      money format
     */
    public static function convertMoneyFormat($number)
    {
        return number_format($number);
    }
    #end

    /**
     * This function return asset with version
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/02/2022
     * Time         11:20:50
     * @param       $src,$version
     * @return      
     */
    public static function assetV($src, $version = '')
    {
        # code... 
        $version = '?v=' . ($version) ? $version : env('JS_VERSION', 1);
        return asset($src . $version);
    }
    #end

    /** 
     * This function return amount by ledger type filter with branch and date
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/03/2022
     * Time         23:46:02
     * @param       $ledger_type, $branch_id = null, $date_from = null, $date_to = null
     * @return      $data
     */
    public static function totalAmountByLedgerType(array $ledger_type, $branch_id = null, $date_from = null, $date_to = null)
    {
        // types
        $types = IncomeExpenseType::whereIn('code', $ledger_type)->select('name', 'code')->get()->toArray();
        // transaction
        $transaction_query = Transaction::query();
        if ($branch_id) {
            $transaction_query->where('branch_id', $branch_id);
        }
        if ($date_from && $date_to) {
            $transaction_query->whereBetween('voucher_date', [date("Y-m-d", strtotime($date_from)), date("Y-m-d", strtotime($date_to))]);
        }
        $transaction_query->whereHas('IncomeExpenseHead.IncomeExpenseType', function ($query) use ($ledger_type) {
            $query->whereIn('code', $ledger_type);
        });
        $transaction_query->with('IncomeExpenseHead', 'IncomeExpenseHead.IncomeExpenseType');
        $transactions = $transaction_query->get()->groupBy('IncomeExpenseHead.IncomeExpenseType.code');
        $items = [];
        foreach ($types as $type) {
            $amount = 0;
            if ($transactions->has($type['code'])) {
                foreach ($transactions[$type['code']] as $transaction) {
                    // dr
                    if ($transaction->IncomeExpenseHead->type == 1) {
                        $amount += $transaction->dr - $transaction->cr;
                    } else {
                        $amount += $transaction->cr - $transaction->dr;
                    }
                }
            }
            $items[$type['code']] = [
                'name' => $type['name'],
                'value' => $amount,
            ];
        }
        $data['items'] = $items;
        return $data;
    }
    #end

    /**
     * This function return branches, bank cache and income expense head from cache data 
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/04/2022
     * Time         22:13:10
     * @param       
     * @return      $data
     */
    public static function __getBranchBankCashIncomeExpenseHead()
    {
        # code... 
        $data = [];
        if (Cache::get('branches') && Cache::get('branches') != null) {
            $data['branches'] = Cache::get('branches');
        } else {
            $data['branches'] = Branch::all();
            Cache::put('branches', $data['branches']);
        }
        if (Cache::get('bank_cashes') && Cache::get('bank_cashes') != null) {
            $data['bank_cashes'] = Cache::get('bank_cashes');
        } else {
            $data['bank_cashes'] = BankCash::all();
            Cache::put('bank_cashes', $data['bank_cashes']);
        }
        if (Cache::get('income_expense_heads') && Cache::get('income_expense_heads') != null) {
            $data['income_expense_heads'] = Cache::get('income_expense_heads');
        } else {
            $data['income_expense_heads'] = IncomeExpenseHead::all();
            Cache::put('income_expense_heads', $data['income_expense_heads']);
        }
        if (Cache::get('income_expense_types') && Cache::get('income_expense_types') != null) {
            $data['income_expense_types'] = Cache::get('income_expense_types');
        } else {
            $data['income_expense_types'] = IncomeExpenseType::all();
            Cache::put('income_expense_types', $data['income_expense_types']);
        }
        if (Cache::get('income_expense_groups') && Cache::get('income_expense_groups') != null) {
            $data['income_expense_groups'] = Cache::get('income_expense_groups');
        } else {
            $data['income_expense_groups'] = IncomeExpenseGroup::all();
            Cache::put('income_expense_groups', $data['income_expense_groups']);
        }
        return $data;
    }
    #end

    /**
     * This function return bank cache details
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/08/2022
     * Time         22:26:52
     * @param      bank_cash_id, branch_id, from, to
     * @return     coloction of bank cache details 
     */
    public static function __bank_cash_details($bank_cash_id = null, $branch_id = null, $from = null, $to = null)
{
    # code...
    $bank_cache_query = BankCash::query();
    
    // Load Transactions and Branch relationships
    $bank_cache_query->with('Transactions', 'Transactions.Branch');
    
    // Filter by bank_cash_id if provided
    if ($bank_cash_id && $bank_cash_id != null) {
        $bank_cache_query->where('id', $bank_cash_id);
    }
    
    $params = [
        'branch_id' => $branch_id,
        'from' => $from,
        'to' => $to,
    ];

    // Add conditions and orderBy to Transactions relationship
    $bank_cache_query->with(['Transactions' => function ($query) use ($params) {
        // Filter by branch_id if provided
        if ($params['branch_id'] && $params['branch_id'] != null) {
            $query->where('branch_id', $params['branch_id']);
        }
        
        // Filter by date range if 'from' and 'to' dates are provided
        if ($params['from'] && $params['from'] != null && $params['to'] && $params['to'] != null) {
            $query->whereBetween('voucher_date', [
                date("Y-m-d", strtotime($params['from'])), 
                date("Y-m-d", strtotime($params['to']))
            ]);
        }
        
        // Order transactions by voucher_date in ascending order
        $query->orderBy('voucher_date', 'asc');
    }]);

    // Return the results
    return $bank_cache_query->get();
}

   /* public static function __bank_cash_details($bank_cash_id = null, $branch_id = null, $from = null, $to = null)
    {
        # code...   
        $bank_cache_query = BankCash::query();
        $bank_cache_query->with('Transactions', 'Transactions.Branch');
        if ($bank_cash_id && $bank_cash_id != null) {
            $bank_cache_query->where('id', $bank_cash_id);
        }
        $params = [
            'branch_id' => $branch_id,
            'from' => $from,
            'to' => $to,
        ];
        $bank_cache_query->with(['Transactions' => function ($query) use ($params) {
            if ($params['branch_id'] && $params['branch_id'] != null) {
                $query->where('branch_id', $params['branch_id']);
            }
            if ($params['from'] && $params['from'] != null && $params['to'] && $params['to'] != null) {
                $query->whereBetween('voucher_date', array(date("Y-m-d", strtotime($params['from'])), date("Y-m-d", strtotime($params['to']))));
            }
        }]);
        return $bank_cache_query->get();
    }
        */
    #end

    /**
     * This function return bank cache balance
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/08/2022
     * Time         22:50:47
     * @param      bank_cash_id, branch_id, from, to
     * @return          
     */
    public static function __bank_cache_balance($bank_cash_id = null, $branch_id = null, $from = null, $to = null)
    {
        # code...   
        $transaction_query = Transaction::query();
        if ($branch_id) {
            $transaction_query->where('branch_id', $branch_id);
        }
        if ($from && $to) {
            $transaction_query->whereBetween('voucher_date', [date("Y-m-d", strtotime($from)), date("Y-m-d", strtotime($to))]);
        }
        $transaction_query->with('Branch', 'IncomeExpenseHead', 'BankCash');
        $transactions = $transaction_query->orderBy('voucher_date', 'DESC')->get();
        $total_bank_cash_balance = 0;
        foreach ($transactions->whereNotNull('bank_cash_id')->groupBy('BankCash.name')  as $bank_cashes_name => $transactions) {
            $bank_dr = 0;
            $bank_cr = 0;
            foreach ($transactions as $transaction) {
                $bank_dr += (int) $transaction->dr;
                $bank_cr += (int) $transaction->cr;
            }
            $total_bank_cash_balance += $bank_cr - $bank_dr;
        }
        return $total_bank_cash_balance;
    }
    #end

    /**
     * This function retrun details transaction by types wise
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/09/2022
     * Time         23:31:08
     * @param       $types_id, $branch_id
     * @return      
     */
    public static function type_wise_transaction_details($types_id = null, $branch_id = null, $from = null, $to = null)
    {
        # code...   
        $transaction_query = Transaction::query();
        $transaction_query->with('IncomeExpenseHead', 'IncomeExpenseHead.IncomeExpenseType');
        $transaction_query->whereNotNull('income_expense_head_id');
        if ($branch_id) {
            $transaction_query->where('branch_id',  $branch_id);
        }
        if ($from && $to) {
            $transaction_query->whereBetween('voucher_date', [date("Y-m-d", strtotime($from)), date("Y-m-d", strtotime($to))]);
        }
        $transaction_query->whereHas('IncomeExpenseHead.IncomeExpenseType', function ($query) use ($types_id) {
            if ($types_id) {
                return $query->where('id', '=', $types_id);
            }
        });
        return $transaction_query->get()->groupBy('IncomeExpenseHead.IncomeExpenseType.name');
    }
    #end

    /**
     * This function return transaction by ledger groups
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       09/11/2022
     * Time         00:09:09
     * @param       group id, branch id, from and to 
     * @return      
     */
    public static function group_wise_transaction_details($group_id = null, $branch_id = null, $from = null, $to = null)
    {
        # code...  
        $transaction_query = Transaction::query();
        $transaction_query->with('IncomeExpenseHead', 'IncomeExpenseHead.IncomeExpenseGroup');
        $transaction_query->whereNotNull('income_expense_head_id');
        if ($branch_id) {
            $transaction_query->where('branch_id',  $branch_id);
        }
        if ($from && $to) {
            $transaction_query->whereBetween('voucher_date', [date("Y-m-d", strtotime($from)), date("Y-m-d", strtotime($to))]);
        }
        $transaction_query->whereHas('IncomeExpenseHead.IncomeExpenseGroup', function ($query) use ($group_id) {
            if ($group_id) {
                return $query->where('id', '=', $group_id);
            }
        });
        return $transaction_query->get()->groupBy('IncomeExpenseHead.IncomeExpenseGroup.name');
    }
    #end

}

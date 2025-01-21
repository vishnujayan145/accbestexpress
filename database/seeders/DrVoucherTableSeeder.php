<?php

namespace Database\Seeders;

use App\Transaction;
use Illuminate\Database\Seeder;

class DrVoucherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::create([
            'voucher_no' => '1',
            'branch_id' => '1',
            'bank_cash_id' => '1',
            'income_expense_head_id' => '1',
            'voucher_type' => 'DV',
            'voucher_date' => '2019-11-25',
            'dr' => '200',
            'particulars' => 'Dr by System',
            'created_by' => 'E-Accounts System',
        ]);
        Transaction::create([
            'voucher_no' => '2',
            'branch_id' => '2',
            'bank_cash_id' => '2',
            'cheque_number' => '112541',
            'income_expense_head_id' => '2',
            'voucher_type' => 'DV',
            'voucher_date' => '2019-11-26',
            'dr' => '500',
            'particulars' => 'Dr by System',
            'created_by' => 'E-Accounts System',
        ]);
        Transaction::create([
            'voucher_no' => '3',
            'branch_id' => '1',
            'bank_cash_id' => '3',
            'cheque_number' => '14521',
            'income_expense_head_id' => '3',
            'voucher_type' => 'DV',
            'voucher_date' => '2019-11-27',
            'dr' => '500',
            'particulars' => 'Dr by System',
            'created_by' => 'E-Accounts System',
        ]);

        Transaction::create([
            'voucher_no' => '3',
            'branch_id' => '1',
            'bank_cash_id' => '3',
            'cheque_number' => '14521',
            'income_expense_head_id' => '4',
            'voucher_type' => 'DV',
            'voucher_date' => '2019-11-27',
            'dr' => '500',
            'particulars' => 'Dr by System',
            'created_by' => 'E-Accounts System',
        ]);
    }
}

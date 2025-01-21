<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\BankCashTableSeeder;
use Database\Seeders\SettingsTableSeeder;
use Database\Seeders\CrVoucherTableSeeder;
use Database\Seeders\DrVoucherTableSeeder;
use Database\Seeders\ContraVoucherTableSeeder;
use Database\Seeders\IncomeExpenseTableSeeder;
use Database\Seeders\JournalVoucherTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        if (env('DEMO_MODE') == true) {
            $this->call(RoleManageTableSeeder::class);
        }
        $this->call(SettingsTableSeeder::class);
        // default system income expense type
        $this->call(IncomeExpenseTableSeeder::class);
        if (env('DEMO_MODE') == true) {
            $this->call(BranchTableSeeder::class);
            $this->call(IncomeExpenseGroupsTableSeeder::class);
            $this->call(IncomeExpenseHeadTableSeeder::class);
        }
        $this->call(BankCashTableSeeder::class);
        if (env('DEMO_MODE') == true) {
            $this->call(DrVoucherTableSeeder::class);
            $this->call(CrVoucherTableSeeder::class);
            $this->call(JournalVoucherTableSeeder::class);
            $this->call(ContraVoucherTableSeeder::class);
        }
    }
}

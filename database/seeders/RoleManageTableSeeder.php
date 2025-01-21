<?php

namespace Database\Seeders;

use App\User;
use App\Profile;
use App\RoleManage;
use Illuminate\Database\Seeder;

class RoleManageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // admin role user
        $admin_user = RoleManage::create([
            'name' => 'Admin',
            'content' => '{"User":["User ",1,1,0,0,0,0,0,0,0],"RoleManager":["Role Manager",1,1,0,0,0,0,0,0,0],"Settings":["Settings",1,1,1,1,1,1,1,1,1],"Branch":["Branch",1,1,1,1,1,1,1,1,1],"LedgerType":["Ledger Type",0,0,0,0,0,0,0,0,0],"LedgerGroup":["Ledger Group",1,1,1,1,1,1,1,1,1],"LedgerName":["Ledger Name",1,1,1,1,1,1,1,1,1],"BankCash":["Bank Cash",1,1,1,1,1,1,1,1,1],"InitialIncomeExpenseHeadBalance":["Initial Income Expense Head Balance",0,0,0,0,0,0,0,0,0],"InitialBankCashBalance":["Initial Bank Cash Balance",0,0,0,0,0,0,0,0,0],"DrVoucher":["Dr Voucher",1,1,1,1,1,1,1,1,1],"CrVoucher":["Cr Voucher",1,1,1,1,1,1,1,1,1],"JnlVoucher":["Jnl Voucher",1,1,1,1,1,1,1,1,1],"ContraVoucher":["Contra Voucher",1,1,1,1,1,1,1,1,1],"Ledger":["Ledger",1,1,1,1,1,1,1,1,1],"TrialBalance":["Trial Balance",1,1,1,1,1,1,1,1,1],"CostOfRevenue":["Cost Of Revenue",1,1,1,1,1,1,1,1,1],"ProfitOrLossAccount":["Profit Or Loss Account",1,1,1,1,1,1,1,1,1],"RetainedEarning":["Retained Earning",1,1,1,1,1,1,1,1,1],"FixedAssetsSchedule":["Fixed Assets Schedule",1,1,1,1,1,1,1,1,1],"StatementOfFinancialPosition":["Statement Of Financial Position",1,1,1,1,1,1,1,1,1],"CashFlow":["Cash Flow",1,1,1,1,1,1,1,1,1],"ReceiveAndPayment":["Receive And Payment",1,1,1,1,1,1,1,1,1],"Notes":["Notes",1,1,1,1,1,1,1,1,1],"GeneralBranch":["General Branch Report",1,1,1,1,1,1,1,1,1],"GeneralLedger":["General Ledger Report",1,1,1,1,1,1,1,1,1],"GeneralBankCash":["General Bank Cash Report",1,1,1,1,1,1,1,1,1],"GeneralVoucher":["General Voucher Report",1,1,1,1,1,1,1,1,1]}',
            'create_by' => 'superadmin@gmail.com',
        ]);
        $userAdmin = User::create([
            'name' => 'admin',
            'email' => 'admin@eaccount.xyz',
            'role_manage_id' => $admin_user->id,
            'password' => bcrypt('1234'),
            'create_by' => 'System',
        ]);
        Profile::create([
            "user_ID" => $userAdmin->id,
            "first_name" => "Sumon",
            "last_name" => "Dada",
            "gender" => 1,
            "designation" => "Software Engineer",
            "phone_number" => "+8801738578683",
            "NID" => "199412478654477",
            "permanent_address" => "Nilkhet",
            "present_address" => "Dhaka,Bangladesh",
            'avatar' => 'upload/avatar/avatar.png',
            "education" => 'B.Sc. in Computer Science & Engineering',
            'description' => ''
        ]);
        // Voucher Manager
        $voucher_manage = RoleManage::create([
            'name' => 'Voucher manager',
            'content' => '{"User":["User ",0,0,0,0,0,0,0,0,0],"RoleManager":["Role Manager",0,0,0,0,0,0,0,0,0],"Settings":["Settings",0,0,0,0,0,0,0,0,0],"Branch":["Branch",0,0,0,0,0,0,0,0,0],"LedgerType":["Ledger Type",0,0,0,0,0,0,0,0,0],"LedgerGroup":["Ledger Group",0,0,0,0,0,0,0,0,0],"LedgerName":["Ledger Name",0,0,0,0,0,0,0,0,0],"BankCash":["Bank Cash",0,0,0,0,0,0,0,0,0],"InitialIncomeExpenseHeadBalance":["Initial Income Expense Head Balance",0,0,0,0,0,0,0,0,0],"InitialBankCashBalance":["Initial Bank Cash Balance",0,0,0,0,0,0,0,0,0],"DrVoucher":["Dr Voucher",1,1,1,1,1,1,1,1,1],"CrVoucher":["Cr Voucher",1,1,1,1,1,1,1,1,1],"JnlVoucher":["Jnl Voucher",1,1,1,1,1,1,1,1,1],"ContraVoucher":["Contra Voucher",1,1,1,1,1,1,1,1,1],"Ledger":["Ledger",0,0,0,0,0,0,0,0,0],"TrialBalance":["Trial Balance",0,0,0,0,0,0,0,0,0],"CostOfRevenue":["Cost Of Revenue",0,0,0,0,0,0,0,0,0],"ProfitOrLossAccount":["Profit Or Loss Account",0,0,0,0,0,0,0,0,0],"RetainedEarning":["Retained Earning",0,0,0,0,0,0,0,0,0],"FixedAssetsSchedule":["Fixed Assets Schedule",0,0,0,0,0,0,0,0,0],"StatementOfFinancialPosition":["Statement Of Financial Position",0,0,0,0,0,0,0,0,0],"CashFlow":["Cash Flow",0,0,0,0,0,0,0,0,0],"ReceiveAndPayment":["Receive And Payment",0,0,0,0,0,0,0,0,0],"Notes":["Notes",0,0,0,0,0,0,0,0,0],"GeneralBranch":["General Branch Report",0,0,0,0,0,0,0,0,0],"GeneralLedger":["General Ledger Report",0,0,0,0,0,0,0,0,0],"GeneralBankCash":["General Bank Cash Report",0,0,0,0,0,0,0,0,0],"GeneralVoucher":["General Voucher Report",0,0,0,0,0,0,0,0,0]}',
            'create_by' => 'superadmin@gmail.com',
        ]);
        $voucher = User::create([
            'name' => 'admin',
            'email' => 'vouchermanage@eaccount.xyz',
            'role_manage_id' => $voucher_manage->id,
            'password' => bcrypt('1234'),
            'create_by' => 'System',
        ]);
        Profile::create([
            "user_ID" => $voucher->id,
            "first_name" => "Md",
            "last_name" => "Abdullah",
            "gender" => 1,
            "designation" => "Software Engineer",
            "phone_number" => "+8801738578683",
            "NID" => "199412478654477",
            "permanent_address" => "Uttara dhaka",
            "present_address" => "Dhaka,Bangladesh",
            'avatar' => 'upload/avatar/avatar.png',
            "education" => 'B.Sc. in Computer Science & Engineering',
            'description' => ''
        ]);

        // Project Manager
        $project_manager = RoleManage::create([
            'name' => 'Project Manager',
            'content' => '{"User":["User ",0,0,0,0,0,0,0,0,0],"RoleManager":["Role Manager",0,0,0,0,0,0,0,0,0],"Settings":["Settings",0,0,0,0,0,0,0,0,0],"Branch":["Branch",1,1,1,1,1,1,1,1,1],"LedgerType":["Ledger Type",0,0,0,0,0,0,0,0,0],"LedgerGroup":["Ledger Group",0,0,0,0,0,0,0,0,0],"LedgerName":["Ledger Name",0,0,0,0,0,0,0,0,0],"BankCash":["Bank Cash",0,0,0,0,0,0,0,0,0],"InitialIncomeExpenseHeadBalance":["Initial Income Expense Head Balance",0,0,0,0,0,0,0,0,0],"InitialBankCashBalance":["Initial Bank Cash Balance",0,0,0,0,0,0,0,0,0],"DrVoucher":["Dr Voucher",0,0,0,0,0,0,0,0,0],"CrVoucher":["Cr Voucher",0,0,0,0,0,0,0,0,0],"JnlVoucher":["Jnl Voucher",0,0,0,0,0,0,0,0,0],"ContraVoucher":["Contra Voucher",0,0,0,0,0,0,0,0,0],"Ledger":["Ledger",0,0,0,0,0,0,0,0,0],"TrialBalance":["Trial Balance",0,0,0,0,0,0,0,0,0],"CostOfRevenue":["Cost Of Revenue",0,0,0,0,0,0,0,0,0],"ProfitOrLossAccount":["Profit Or Loss Account",0,0,0,0,0,0,0,0,0],"RetainedEarning":["Retained Earning",0,0,0,0,0,0,0,0,0],"FixedAssetsSchedule":["Fixed Assets Schedule",0,0,0,0,0,0,0,0,0],"StatementOfFinancialPosition":["Statement Of Financial Position",0,0,0,0,0,0,0,0,0],"CashFlow":["Cash Flow",0,0,0,0,0,0,0,0,0],"ReceiveAndPayment":["Receive And Payment",0,0,0,0,0,0,0,0,0],"Notes":["Notes",0,0,0,0,0,0,0,0,0],"GeneralBranch":["General Branch Report",0,0,0,0,0,0,0,0,0],"GeneralLedger":["General Ledger Report",0,0,0,0,0,0,0,0,0],"GeneralBankCash":["General Bank Cash Report",0,0,0,0,0,0,0,0,0],"GeneralVoucher":["General Voucher Report",0,0,0,0,0,0,0,0,0]}',
            'create_by' => 'superadmin@gmail.com',
        ]);
        $voucher = User::create([
            'name' => 'admin',
            'email' => 'projectmanager@eaccount.xyz',
            'role_manage_id' => $project_manager->id,
            'password' => bcrypt('1234'),
            'create_by' => 'System',
        ]);
        Profile::create([
            "user_ID" => $voucher->id,
            "first_name" => "Md",
            "last_name" => "Abdullah",
            "gender" => 1,
            "designation" => "Software Engineer",
            "phone_number" => "+8801738578683",
            "NID" => "199412478654477",
            "permanent_address" => "Uttara dhaka",
            "present_address" => "Dhaka,Bangladesh",
            'avatar' => 'upload/avatar/avatar.png',
            "education" => 'B.Sc. in Computer Science & Engineering',
            'description' => ''
        ]);
    }
}

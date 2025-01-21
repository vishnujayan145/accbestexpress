<?php

namespace Database\Seeders;

use App\User;
use App\Profile;
use App\RoleManage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // first drop all user and role for db
        DB::table('role_manages')->where('id', '!=', 0)->delete();
        DB::table('users')->where('id', '!=', 0)->delete();

        // super admin
        $superadmin_roles = RoleManage::create([
            'name' => 'Super Admin',
            'content' => '{"User":["User ",1,1,1,1,1,1,1,1,1],"RoleManager":["Role Manager",1,1,1,1,1,1,1,1,1],"Settings":["Settings",1,1,1,1,1,1,1,1,1],"Branch":["Branch",1,1,1,1,1,1,1,1,1],"LedgerType":["Ledger Type",1,1,1,1,1,1,1,1,1],"LedgerGroup":["Ledger Group",1,1,1,1,1,1,1,1,1],"LedgerName":["Ledger Name",1,1,1,1,1,1,1,1,1],"BankCash":["Bank Cash",1,1,1,1,1,1,1,1,1],"InitialIncomeExpenseHeadBalance":["Initial Income Expense Head Balance",1,1,1,1,1,1,1,1,1],"InitialBankCashBalance":["Initial Bank Cash Balance",1,1,1,1,1,1,1,1,1],"DrVoucher":["Dr Voucher",1,1,1,1,1,1,1,1,1],"CrVoucher":["Cr Voucher",1,1,1,1,1,1,1,1,1],"JnlVoucher":["Jnl Voucher",1,1,1,1,1,1,1,1,1],"ContraVoucher":["Contra Voucher",1,1,1,1,1,1,1,1,1],"Ledger":["Ledger",1,1,1,1,1,1,1,1,1],"TrialBalance":["Trial Balance",1,1,1,1,1,1,1,1,1],"CostOfRevenue":["Cost Of Revenue",1,1,1,1,1,1,1,1,1],"ProfitOrLossAccount":["Profit Or Loss Account",1,1,1,1,1,1,1,1,1],"RetainedEarning":["Retained Earning",1,1,1,1,1,1,1,1,1],"FixedAssetsSchedule":["Fixed Assets Schedule",1,1,1,1,1,1,1,1,1],"StatementOfFinancialPosition":["Statement Of Financial Position",1,1,1,1,1,1,1,1,1],"CashFlow":["Cash Flow",1,1,1,1,1,1,1,1,1],"ReceiveAndPayment":["Receive And Payment",1,1,1,1,1,1,1,1,1],"Notes":["Notes",1,1,1,1,1,1,1,1,1],"GeneralBranch":["General Branch Report",1,1,1,1,1,1,1,1,1],"GeneralLedger":["General Ledger Report",1,1,1,1,1,1,1,1,1],"GeneralBankCash":["General Bank Cash Report",1,1,1,1,1,1,1,1,1],"GeneralVoucher":["General Voucher Report",1,1,1,1,1,1,1,1,1]}',
            'create_by' => 'superadmin@gmail.com',
        ]);
        $userSuperAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@eaccount.xyz',
            'role_manage_id' => $superadmin_roles->id,
            'password' => (env('DEMO_MODE') == true) ?  bcrypt('mamun2074') : bcrypt('1234'),
            'create_by' => 'System',
        ]);
        Profile::create([
            "user_ID" => $userSuperAdmin->id,
            "first_name" => "S.M",
            "last_name" => "Abid",
            "gender" => 1,
            "designation" => "Software Engineer",
            "phone_number" => "+8801738578683",
            "NID" => "199412478654477",
            "permanent_address" => "PS: Raygonj, District: Sirajgonj",
            "present_address" => "Dhaka,Bangladesh",
            'avatar' => 'upload/avatar/avatar.png',
            "education" => 'B.Sc. in Computer Science & Engineering',
            'description' => ''
        ]);
    }
}

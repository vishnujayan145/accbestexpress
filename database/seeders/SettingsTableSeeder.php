<?php

namespace Database\Seeders;

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'settings_name' => 'General Settings',
            'content' => '{"company_name":"E-Accounts","contract_person":"+8801738578683","email":"mahmud2074office@gmail.com","phone":"+8801521497833","address_1":"Wakil Tower [Level 6] Ta 131 Culsan-Badda Link Road Gulsan 1 Dhaka 1212 Bangladesh","address_2":null,"city":"Dhaka","state":"Gulsan 1","zip_code":"1212","company_logo":"upload\/company-logo\/e.png"}'
        ]);

        Setting::create([
            'settings_name' => 'System Settings',
            'content' => '{"date_format":"M d, Y","timezone":"Asia\/Dhaka","currency_code":"BDT","currency_symbol":"\u09f3","is_code":"code","is_rtl":"No","currency_position":"Suffix","fixed_asset_schedule_starting_date":"2000-01-01"}'
        ]);
    }
}

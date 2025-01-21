<?php

namespace App\Http\Middleware;

use Closure;

use App\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Cache::get('general_settings') && Cache::get('general_settings') != null) {
            $general = json_decode(Cache::get('general_settings'), true);
        } else {
            $general_setting = Setting::where('settings_name', 'General Settings')->get()->first();
            $general = json_decode($general_setting->content, true);
            Cache::put('general_settings', $general_setting->content);
        }

        if (Cache::get('system_settings') && Cache::get('system_settings') != null) {
            $system = json_decode(Cache::get('system_settings'), true);
        } else {
            $system_setting = Setting::where('settings_name', 'System Settings')->get()->first();
            $system = json_decode($system_setting->content, true);
            Cache::put('system_settings', $system_setting->content);
        }

        //        Time Zone
        date_default_timezone_set($system['timezone']);
        config([
            'settings.company_name' => $general['company_name'],
            'settings.contract_person' => $general['contract_person'],
            'settings.email' => $general['email'],
            'settings.phone' => $general['phone'],
            'settings.address_1' => $general['address_1'],
            'settings.address_2' => $general['address_2'],
            'settings.city' => $general['city'],
            'settings.state' => $general['state'],
            'settings.zip_code' => $general['zip_code'],
            'settings.company_logo' => $general['company_logo'],

            'settings.date_format' => $system['date_format'],
            'settings.currency_code' => $system['currency_code'],
            'settings.currency_symbol' => $system['currency_symbol'],
            'settings.is_code' => $system['is_code'],
            'settings.currency_position' => $system['currency_position'],
            'settings.fixed_asset_schedule_starting_date' => $system['fixed_asset_schedule_starting_date'],
        ]);
        return $next($request);
    }
}

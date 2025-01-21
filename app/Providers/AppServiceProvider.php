<?php

namespace App\Providers;

use App\Setting;
use App\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        if (!app()->runningInConsole()) {
            // Share settings
            if (Cache::get('system_settings') && Cache::get('system_settings') != null) {
                $system_settings = json_decode(Cache::get('system_settings'), true);
            } else {
                $system_settings_info = Setting::where('settings_name', 'System Settings')->get()->first();
                $system_settings = json_decode($system_settings_info->content, true);
            }
            if (Cache::get('general_settings') && Cache::get('general_settings') != null) {
                $general_settings = json_decode(Cache::get('general_settings'), true);
            } else {
                $system_settings_info = Setting::where('settings_name', 'General Settings')->get()->first();
                $general_settings = json_decode($system_settings_info->content, true);
            }
            View::share('general_settings', $general_settings);
            View::share('system_settings', $system_settings);
            if ($system_settings['is_rtl'] == 'Yes') {
                $is_rtl = 1;
            } else {
                $is_rtl = 0;
            }
            View::share('is_rtl', $is_rtl);

            // For language support
            view()->composer('*', function ($view) {
                if (auth()->check()) {
                    $language = (auth()->user()->language) ? auth()->user()->language :  Language::where('is_default', 1)->first();
                    // dd($language);
                    if ($language) {
                        App::setLocale($language->code);
                    } else {
                        App::setLocale('main');
                    }
                } else {
                    App::setLocale('main');
                }
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

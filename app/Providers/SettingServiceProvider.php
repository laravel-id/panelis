<?php

namespace App\Providers;

use App\Models\Setting;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $hasSetting = Cache::remember('has_setting', now()->addDay(), function (): bool {
            return Schema::hasTable('settings');
        });

        if ($hasSetting) {
            Setting::getAll()->each(function (Setting $setting): void {
                Config::set($setting->key, $setting->value);
            });

            if (Auth::check()) {
                Setting::getByUser(Auth::id())->each(function (Setting $setting): void {
                    // override config from db with user's
                    Config::set($setting->key, $setting->value);
                });
            }
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $lang) {
            $lang->locales(config('app.locales', [config('app.locale', 'en')]))
                ->circular();
        });
    }
}

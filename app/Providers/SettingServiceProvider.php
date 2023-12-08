<?php

namespace App\Providers;

use App\Models\Module;
use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        return;
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

        $hasModule = Cache::remember('has_module', now()->addDay(), function (): bool {
            return Schema::hasTable('modules');
        });

        if ($hasModule) {
            Module::query()
                ->get()
                ->each(function (Module $module): void {
                    $name = sprintf('module.%s', Str::snake($module->name));

                    Config::set($name, (bool) $module->is_enabled);
                });
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $lang) {
            $lang->locales(config('app.locales', [config('app.locale', 'en')]))
                ->circular();
        });
    }
}

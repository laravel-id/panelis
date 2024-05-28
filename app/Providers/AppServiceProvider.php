<?php

namespace App\Providers;

use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->overrideConfig();

        LanguageSwitch::configureUsing(function (LanguageSwitch $lang) {
            $lang->locales(config('app.locales'))
                ->circular();
        });
    }

    private function overrideConfig(): void
    {
        foreach (Setting::getAll() as $setting) {
            $value = $setting->value;
            if ($value === '1' || $value === '0') {
                $value = boolval($value);
            }
            Config::set($setting->key, $value);
        }
    }
}

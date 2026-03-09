<?php

namespace Modules\Setting;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'setting');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->loadViewsFrom(__DIR__.'/Views', 'setting');
    }

    public function register(): void
    {
        //
    }
}

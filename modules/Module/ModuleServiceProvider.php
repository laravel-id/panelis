<?php

namespace Modules\Module;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'module');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    public function register(): void {}
}

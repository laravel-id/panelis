<?php

namespace Modules\Branch;

use Illuminate\Support\ServiceProvider;

class BranchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'branch');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    public function register(): void {}
}

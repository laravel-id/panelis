<?php

namespace Modules\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'user');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    public function register(): void {}
}

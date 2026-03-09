<?php

namespace Modules\Database;

use Illuminate\Support\ServiceProvider;
use Modules\Database\Commands\BackupCommand;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'database');

        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupCommand::class,
            ]);
        }
    }

    public function register(): void {}
}

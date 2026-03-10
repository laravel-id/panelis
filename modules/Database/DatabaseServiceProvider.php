<?php

namespace Modules\Database;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Modules\Database\Commands\BackupCommand;
use Modules\Database\Services\Database\Contracts\Database;
use Modules\Database\Services\Database\Database as DatabaseManager;
use Modules\Database\Services\OAuth\OAuth;
use Modules\Database\Services\OAuth\OAuthFactory;

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

    public function register(): void
    {
        $this->app->singleton(Database::class, function (Application $app): Database {
            return $app->make(DatabaseManager::class)->driver(config('database.default'));
        });

        $this->app->singleton(OAuth::class, function (Application $app): OAuthFactory {
            return new OAuthFactory($app);
        });
    }
}

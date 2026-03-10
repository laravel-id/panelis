<?php

namespace Modules\Location;

use Illuminate\Support\ServiceProvider;
use Modules\Location\Commands\ImportLocationCommand;

class LocationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'location');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportLocationCommand::class,
            ]);
        }
    }

    public function register(): void {}
}

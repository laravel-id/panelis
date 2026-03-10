<?php

namespace Modules\Job;

use Illuminate\Support\ServiceProvider;

class JobServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'job');
    }

    public function register(): void {}
}

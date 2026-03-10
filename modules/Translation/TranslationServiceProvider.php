<?php

namespace Modules\Translation;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/lang', 'translation');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register(): void {}
}

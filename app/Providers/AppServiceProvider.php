<?php

namespace App\Providers;

use App\Filament\Clusters\Settings\Enums\NumberFormat;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactory;
use App\Services\OAuth\OAuth;
use App\Services\OAuth\OAuthFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Database::class, function (Application $app): DatabaseFactory {
            return new DatabaseFactory($app);
        });

        $this->app->singleton(OAuth::class, function (Application $app): OAuthFactory {
            return new OAuthFactory($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->isProduction()) {
            Model::preventLazyLoading();
            Model::preventAccessingMissingAttributes();
            Model::preventSilentlyDiscardingAttributes();
        }

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale): string {
            if (config('app.translation_debug')) {
                Log::warning(sprintf('Missing translation key: %s', $key), [
                    'key' => $key,
                    'text' => __($key, $replacements),
                    'locale' => $locale,
                ]);
            }

            return $key;
        });

        Number::macro('money', function (
            int|float $amount,
            ?string $format = null,
            ?string $symbol = null,
            ?bool $isSymbolSuffix = null,
        ): string {
            $format = NumberFormat::tryFrom($format ?? config('app.number_format', NumberFormat::Plain->value));
            $isSymbolSuffix = $isSymbolSuffix ?? config('app.number_symbol_suffix', false);
            $symbol ??= config('app.currency_symbol');

            $number = number_format($amount, ...$format->display());

            if ($isSymbolSuffix) {
                return sprintf('%s %s', $number, $symbol);
            }

            return sprintf('%s%s', $symbol, $number);
        });
    }
}

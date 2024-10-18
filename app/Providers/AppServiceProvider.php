<?php

namespace App\Providers;

use App\Facades\Event\Schedule;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactory;
use App\Services\OAuth\OAuth;
use App\Services\OAuth\OAuthFactory;
use App\Services\Payments\Factory as PaymentFactory;
use App\Services\Payments\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
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
        $this->app->bind('schedule', fn (): Schedule => new Schedule);

        $this->app->bind(Database::class, function (): ?object {
            return DatabaseFactory::make();
        });

        $this->app->singleton(OAuth::class, function (Application $app): OAuthFactory {
            return new OAuthFactory($app);
        });

        $this->app->bind(Payment::class, function (Application $app): PaymentFactory {
            return new PaymentFactory($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl('https://schedules.run');
        }

        Number::macro('money', function (
            int|float $amount,
            ?string $format = null,
            ?string $symbol = null,
            ?bool $isSymbolSuffix = null,
        ): string {
            $format = $format ?? config('app.number_format', '');
            $isSymbolSuffix = $isSymbolSuffix ?? config('app.number_symbol_suffix', false);

            if (empty($format)) {
                if ($isSymbolSuffix) {
                    return sprintf('%.2f %s', $amount, $symbol);
                }

                return sprintf('%s%.2f', $symbol, $amount);
            }

            $format = explode(' ', $format, 3);
            $format = array_map(function ($value) {
                if (is_numeric($value)) {
                    return intval($value);
                }

                return $value;
            }, $format);

            $currency = [
                $symbol = $symbol ?? config('app.currency_symbol'),
                $number = number_format($amount, ...$format),
            ];

            if ($isSymbolSuffix) {
                $placeholder = '%s %s';
                $currency = [$number, $symbol];
            }

            return vsprintf($placeholder ?? '%s%s', $currency);
        });
    }
}

<?php

namespace App\Providers;

use App\Facades\Event\Schedule;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactory;
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
        $this->app->bind(Database::class, function ($app): ?object {
            return DatabaseFactory::make();
        });

        $this->app->bind('schedule', fn () => new Schedule);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
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

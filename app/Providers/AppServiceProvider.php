<?php

namespace App\Providers;

use App\Models\Event\Schedule;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactory;
use App\Services\OAuth\OAuth;
use App\Services\OAuth\OAuthFactory;
use App\Services\OAuth\Vendors\Dropbox;
use App\Services\OAuth\Vendors\GoogleDrive;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('schedule', fn () => new Schedule);

        $this->app->bind(Database::class, function (): ?object {
            return DatabaseFactory::make();
        });

        $this->app->singleton(OAuth::class, function (Application $app): OAuthFactory {
            $manager = new OAuthFactory($app);

            $manager->extend(OAuthFactory::Dropbox, fn (): OAuth => new Dropbox);
            $manager->extend(OAuthFactory::GoogleDrive, fn (): OAuth => new GoogleDrive);

            return $manager;
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

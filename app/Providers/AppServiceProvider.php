<?php

namespace App\Providers;

use App\Models\Module;
use App\Models\Setting;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactory;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private function overrideDefaultConfig(): void
    {
        $hasSetting = Cache::rememberForever('has_setting', function (): bool {
            return Schema::hasTable((new Setting)->getTable());
        });

        if (! $hasSetting) {
            return;
        }

        foreach (Setting::getAll() as $setting) {
            $value = $setting->value;
            if ($value === '1' || $value === '0') {
                $value = boolval($value);
            }

            Config::set($setting->key, $value);
        }

    }

    private function registerCustomNavigations()
    {
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make(__('common.source_code'))
                    ->sort(200)
                    ->group(__('setting.navigation'))
                    ->url('https://github.com/laravel-id/panelis')
                    ->activeIcon('heroicon-s-code-bracket')
                    ->icon('heroicon-o-code-bracket'),

            ]);
        });
    }

    private function registerModules()
    {
        if (Schema::hasTable((new Module)->getTable())) {
            $modules = Cache::remember('modules', now()->addHour(), function () {
                return Module::select('name', 'is_enabled')
                    ->get();
            });

            foreach ($modules as $module) {
                config()->set(sprintf('modules.%s', strtolower($module->name)), $module->is_enabled);
            }
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Database::class, function ($app): ?object {
            return DatabaseFactory::make();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->overrideDefaultConfig();

        $this->registerModules();
        $this->registerModules();

        LanguageSwitch::configureUsing(function (LanguageSwitch $lang) {
            $lang->locales(['id', 'en'])
                ->circular();
        });

        Number::macro('money', function (
            int|float $amount,
            ?string $format = null,
            ?string $symbol = null,
            ?bool $isSymbolSuffix = null,
        ): string {
            $format = $format ?? config('app.number_format', '');
            $isSymbolSuffix = $isSymbolSuffix ?? config('app.symbol_suffix', false);

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

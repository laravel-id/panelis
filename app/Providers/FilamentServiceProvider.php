<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->setLocale(app('app.locale'));

        FilamentTimezone::set(Config::get('app.datetime.timezone'));

        LanguageSwitch::configureUsing(function (LanguageSwitch $lang) {
            $lang->locales(config('app.locales', [config('app.locale', 'en')]))
                ->circular();
        });

        TextColumn::macro('makeSinceDate', function (string $name, ?string $label = null): TextColumn {
            return TextColumn::make($name)
                ->label($label)
                ->sortable()
                ->since()
                ->dateTimeTooltip(Config::get('app.datetime.format'));
        });

        TextEntry::macro('makeSinceDate', function (string $name, ?string $label = null): TextEntry {
            return TextEntry::make($name)
                ->label($label)
                ->since()
                ->dateTimeTooltip(Config::get('app.datetime.format'));
        });
    }
}

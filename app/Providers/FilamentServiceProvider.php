<?php

namespace App\Providers;

use Filament\Tables\Columns\TextColumn;
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
        TextColumn::macro('makeSinceDate', function (string $name, ?string $label = null): TextColumn {
            return TextColumn::make($name)
                ->label($label)
                ->sortable()
                ->since(get_timezone())
                ->dateTimeTooltip(get_datetime_format(), get_timezone());
        });
    }
}

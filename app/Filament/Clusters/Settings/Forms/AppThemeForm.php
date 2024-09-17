<?php

namespace App\Filament\Clusters\Settings\Forms;

use App\Enums\ThemeMode;
use App\Filament\Clusters\Settings\Enums\PicoTheme;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;

class AppThemeForm
{
    public static function schema(): array
    {
        return [
            Select::make('color.theme')
                ->label(__('setting.theme_color'))
                ->searchable()
                ->native(false)
                ->allowHtml()
                ->enum(PicoTheme::class)
                ->options(PicoTheme::options()),

            Radio::make('color.mode')
                ->label(__('setting.theme_mode'))
                ->options(ThemeMode::options()),
        ];
    }
}

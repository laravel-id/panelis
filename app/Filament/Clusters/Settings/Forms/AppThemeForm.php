<?php

namespace App\Filament\Clusters\Settings\Forms;

use App\Filament\Clusters\Settings\Enums\PicoTheme;
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
                ->options(PicoTheme::options(true)),
        ];
    }
}

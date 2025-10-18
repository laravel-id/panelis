<?php

namespace App\Filament\Clusters\Settings\Forms\General;

use Filament\Forms\Components\Toggle;

class DebugForm
{
    public static function schema(): array
    {
        return [
            Toggle::make('app.debug')
                ->label(__('setting.general.app_debug_enabled'))
                ->helperText(fn (): ?string => app()->isProduction() ? __('setting.helper_app_debug') : null),

            Toggle::make('telescope.enabled')
                ->label(__('setting.general.telescope_enabled')),
        ];
    }
}

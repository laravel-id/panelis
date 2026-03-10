<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\General;

use Filament\Forms\Components\Toggle;

class DebugForm
{
    public static function schema(): array
    {
        return [
            Toggle::make('app.debug')
                ->label(__('setting::setting.general.app_debug_enabled'))
                ->helperText(fn (): ?string => app()->isProduction() ? __('setting::setting.helper_app_debug') : null),

            Toggle::make('telescope.enabled')
                ->label(__('setting::setting.general.telescope_enabled')),
        ];
    }
}

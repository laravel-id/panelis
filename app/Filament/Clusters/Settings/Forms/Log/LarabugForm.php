<?php

namespace App\Filament\Clusters\Settings\Forms\Log;

use App\Filament\Clusters\Settings\Enums\LarabugEnvironment;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;

class LarabugForm
{
    public static function make(): array
    {
        return [
            TextInput::make('larabug.login_key')
                ->label(__('setting.log_larabug_key'))
                ->password()
                ->required(),

            TextInput::make('larabug.project_key')
                ->label(__('setting.log_larabug_project_key'))
                ->password()
                ->required(),

            CheckboxList::make('larabug.environments')
                ->label(__('setting.larabug_environment'))
                ->helperText(__('setting.helper_larabug_environment'))
                ->options(LarabugEnvironment::options())
                ->required(),
        ];
    }
}

<?php

namespace App\Filament\Clusters\Settings\Forms\Log;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;

class NightwatchForm
{
    public static function schema(): array
    {
        return [
            Checkbox::make('nightwatch.enabled')
                ->label(__('setting.nightwatch_enabled'))
                ->live(),

            TextInput::make('nightwatch.token')
                ->label(__('setting.nightwatch_token'))
                ->password()
                ->revealable()
                ->required()
                ->disabled(fn (Get $get): bool => ! $get('nightwatch.enabled')),

            TextEntry::make('nightwatch.server')
                ->label(__('setting.nightwatch_server'))
                ->state(function (): string {
                    return config('nightwatch.server');
                }),

            Fieldset::make(__('setting.nightwatch_sampling'))
                ->columns(3)
                ->disabled(fn (Get $get): bool => ! $get('nightwatch.enabled'))
                ->schema([
                    TextInput::make('nightwatch.sampling.requests')
                        ->label(__('setting.nightwatch_sampling_requests'))
                        ->default(1)
                        ->required()
                        ->numeric(),

                    TextInput::make('nightwatch.sampling.commands')
                        ->label(__('setting.nightwatch_sampling_commands'))
                        ->required()
                        ->numeric(),

                    TextInput::make('nightwatch.sampling.exceptions')
                        ->label(__('setting.nightwatch_sampling_exceptions'))
                        ->required()
                        ->numeric(),
                ]),
        ];
    }
}

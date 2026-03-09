<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\Log;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Setting\Panel\Clusters\Settings\Enums\LogLevel;

class PapertailForm
{
    public static function schema(): array
    {
        return [
            Select::make('logging.channels.papertrail.level')
                ->label(__('setting::setting.log.level'))
                ->options(LogLevel::class)
                ->searchable()
                ->required()
                ->dehydrateStateUsing(function (?LogLevel $state): ?string {
                    if ($state instanceof LogLevel) {
                        return $state->value;
                    }

                    return $state;
                })
                ->enum(LogLevel::class),

            TextInput::make('logging.channels.papertrail.url')
                ->label(__('setting::setting.log.papertrail_url'))
                ->url()
                ->required(),

            TextInput::make('logging.channels.papertrail.port')
                ->label(__('setting::setting.log.papertrail_port'))
                ->numeric()
                ->required(),
        ];
    }
}

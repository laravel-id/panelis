<?php

namespace App\Filament\Clusters\Settings\Forms\Log;

use App\Filament\Clusters\Settings\Enums\LogLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PapertailForm
{
    public static function make(): array
    {
        return [
            Select::make('logging.channels.papertrail.level')
                ->label(__('setting.log.level'))
                ->options(LogLevel::options())
                ->searchable()
                ->required()
                ->enum(LogLevel::class),

            TextInput::make('logging.channels.papertrail.url')
                ->label(__('setting.log.papertrail_url'))
                ->url()
                ->required(),

            TextInput::make('logging.channels.papertrail.port')
                ->label(__('setting.log.papertrail_port'))
                ->numeric()
                ->required(),
        ];
    }
}

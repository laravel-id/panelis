<?php

namespace App\Filament\Clusters\Settings\Forms\Log;

use App\Filament\Clusters\Settings\Enums\LogLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SlackForm
{
    public static function make(): array
    {
        return [
            Select::make('logging.channels.slack.level')
                ->label(__('setting.log.level'))
                ->options(LogLevel::class)
                ->searchable()
                ->required()
                ->enum(LogLevel::class),

            TextInput::make('logging.channels.slack.url')
                ->label(__('setting.log.slack_webhook_url'))
                ->hint(
                    str(__('setting.log.slack_webhook_hint'))
                        ->inlineMarkdown()
                        ->toHtmlString()
                )
                ->url()
                ->required(),

            TextInput::make('logging.channels.slack.username')
                ->label(__('setting.log.slack_username'))
                ->string()
                ->required(),
        ];
    }
}

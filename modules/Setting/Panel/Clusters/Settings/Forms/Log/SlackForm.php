<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\Log;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Setting\Panel\Clusters\Settings\Enums\LogLevel;

class SlackForm
{
    public static function schema(): array
    {
        return [
            Select::make('logging.channels.slack.level')
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

            TextInput::make('logging.channels.slack.url')
                ->label(__('setting::setting.log.slack_webhook_url'))
                ->hint(
                    str(__('setting::setting.log.slack_webhook_hint'))
                        ->inlineMarkdown()
                        ->toHtmlString()
                )
                ->url()
                ->required(),

            TextInput::make('logging.channels.slack.username')
                ->label(__('setting::setting.log.slack_username'))
                ->string()
                ->required(),
        ];
    }
}

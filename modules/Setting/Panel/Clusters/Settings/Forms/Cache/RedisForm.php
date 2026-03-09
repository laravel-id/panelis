<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;

class RedisForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('database.redis.cache.host')
                ->label(__('setting::setting.cache.redis.host'))
                ->required(),

            TextInput::make('database.redis.cache.port')
                ->label(__('setting::setting.cache.redis.port'))
                ->required(),

            TextInput::make('database.redis.cache.database')
                ->label(__('setting::setting.cache.redis.database'))
                ->numeric()
                ->required(),

            TextInput::make('database.redis.cache.username')
                ->label(__('setting::setting.cache.redis.username'))
                ->string(),

            TextInput::make('database.redis.cache.password')
                ->label(__('setting::setting.cache.redis.password'))
                ->string()
                ->password()
                ->revealable(),
        ];
    }
}

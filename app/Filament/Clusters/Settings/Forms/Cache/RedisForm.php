<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;

class RedisForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('database.redis.cache.host')
                ->label(__('setting.cache.redis_host'))
                ->required(),

            TextInput::make('database.redis.cache.port')
                ->label(__('setting.cache.redis_port'))
                ->required(),

            TextInput::make('database.redis.cache.database')
                ->label(__('setting.cache.redis_database'))
                ->numeric()
                ->required(),

            TextInput::make('database.redis.cache.username')
                ->label(__('setting.cache.redis_username'))
                ->string(),

            TextInput::make('database.redis.cache.password')
                ->label(__('setting.cache.redis_password'))
                ->string()
                ->password()
                ->revealable(),
        ];
    }
}

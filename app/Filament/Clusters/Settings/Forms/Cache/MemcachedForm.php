<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;

class MemcachedForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('cache.stores.memcached.servers.host')
                ->label(__('setting.cache.memcached.host'))
                ->required(),

            TextInput::make('cache.stores.memcached.servers.port')
                ->label(__('setting.cache.memcached.port'))
                ->numeric()
                ->required(),

            TextInput::make('cache.stores.memcached.sasl.username')
                ->label(__('setting.cache.memcached.username'))
                ->numeric()
                ->nullable(),

            TextInput::make('cache.stores.memcached.sasl.password')
                ->label(__('setting.cache.memcached.password'))
                ->password()
                ->revealable()
                ->nullable(),
        ];
    }
}

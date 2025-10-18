<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;

class MemcachedForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('cache.stores.memcached.servers.host')
                ->label(__('setting.cache.memcached_host'))
                ->required(),

            TextInput::make('cache.stores.memcached.servers.port')
                ->label(__('setting.cache.memcached_port'))
                ->numeric()
                ->required(),

            TextInput::make('cache.stores.memcached.sasl.username')
                ->label(__('setting.cache.memcached_username'))
                ->numeric()
                ->nullable(),

            TextInput::make('cache.stores.memcached.sasl.password')
                ->label(__('setting.cache.memcached_password'))
                ->password()
                ->revealable()
                ->nullable(),
        ];
    }
}

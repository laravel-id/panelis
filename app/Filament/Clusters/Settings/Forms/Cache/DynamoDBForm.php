<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;

class DynamoDBForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('cache.stores.dynamodb.key')
                ->label(__('setting.cache_dynamodb_key'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.secret')
                ->label(__('setting.cache_dynamodb_secret'))
                ->password()
                ->revealable()
                ->required(),

            TextInput::make('cache.stores.dynamodb.region')
                ->label(__('setting.cache_dynamodb_region'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.table')
                ->label(__('setting.cache_dynamodb_table'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.endpoint')
                ->label(__('setting.cache_dynamodb_endpoint'))
                ->required(),
        ];
    }
}

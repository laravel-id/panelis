<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use App\Filament\Clusters\Settings\Enums\CacheDriver;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;

class DynamoDBForm
{
    public static function schema(): array
    {
        return [
            Callout::make(__('setting.cache.dynamodb.no_package_title'))
                ->description(__('setting.cache.dynamodb.no_package_description'))
                ->warning()
                ->hidden(CacheDriver::DynamoDB->isInstalled()),

            TextInput::make('cache.stores.dynamodb.key')
                ->label(__('setting.cache.dynamodb.key'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.secret')
                ->label(__('setting.cache.dynamodb.secret'))
                ->password()
                ->revealable()
                ->required(),

            TextInput::make('cache.stores.dynamodb.region')
                ->label(__('setting.cache.dynamodb.region'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.table')
                ->label(__('setting.cache.dynamodb.table'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.endpoint')
                ->label(__('setting.cache.dynamodb.endpoint'))
                ->required(),
        ];
    }
}

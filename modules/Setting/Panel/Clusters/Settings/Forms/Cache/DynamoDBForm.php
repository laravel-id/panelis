<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Modules\Setting\Panel\Clusters\Settings\Enums\CacheDriver;

class DynamoDBForm
{
    public static function schema(): array
    {
        return [
            Callout::make(__('setting::setting.cache.dynamodb.no_package_title'))
                ->description(__('setting::setting.cache.dynamodb.no_package_description'))
                ->warning()
                ->hidden(CacheDriver::DynamoDB->isInstalled()),

            TextInput::make('cache.stores.dynamodb.key')
                ->label(__('setting::setting.cache.dynamodb.key'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.secret')
                ->label(__('setting::setting.cache.dynamodb.secret'))
                ->password()
                ->revealable()
                ->required(),

            TextInput::make('cache.stores.dynamodb.region')
                ->label(__('setting::setting.cache.dynamodb.region'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.table')
                ->label(__('setting::setting.cache.dynamodb.table'))
                ->required(),

            TextInput::make('cache.stores.dynamodb.endpoint')
                ->label(__('setting::setting.cache.dynamodb.endpoint'))
                ->required(),
        ];
    }
}

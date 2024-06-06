<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\Enums\CacheDriver;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Cache extends Page implements HasForms, Settings\HasUpdateableForm
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 8;

    public array $cache;

    public array $database;

    public bool $isButtonDisabled = false;

    private function driverSection(): Section
    {
        return Section::make(__('setting.cache'))
            ->description(__('setting.cache_section_description'))
            ->schema([
                Radio::make('cache.default')
                    ->label(__('setting.cache_driver'))
                    ->options(CacheDriver::options())
                    ->descriptions(CacheDriver::getDescriptions())
                    ->live()
                    ->required(),
            ]);
    }

    private function memcachedSection(): Section
    {
        return Section::make('setting.cache_memcached')
            ->description(__('setting.cache_memcached_description'))
            ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Memcached->value)
            ->schema([
                TextInput::make('cache.stores.memcached.servers.host')
                    ->label(__('setting.cache_memcached_host'))
                    ->required(),

                TextInput::make('cache.stores.memcached.servers.port')
                    ->label(__('setting.cache_memcached_port'))
                    ->numeric()
                    ->required(),

                TextInput::make('cache.stores.memcached.sasl.username')
                    ->label(__('setting.cache_memcached_username'))
                    ->numeric()
                    ->nullable(),

                TextInput::make('cache.stores.memcached.sasl.password')
                    ->label(__('setting.cache_memcached_password'))
                    ->password()
                    ->revealable()
                    ->nullable(),
            ]);
    }

    private function redisSection(): Section
    {
        return

            Section::make('setting.cache_redis')
                ->description(__('setting.cache_redis_description'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Redis->value)
                ->schema([
                    TextInput::make('database.redis.cache.host')
                        ->label(__('setting.cache_redis_host'))
                        ->required(),

                    TextInput::make('database.redis.cache.port')
                        ->label(__('setting.cache_redis_port'))
                        ->required(),

                    TextInput::make('database.redis.cache.database')
                        ->label(__('setting.cache_redis_database'))
                        ->numeric()
                        ->required(),

                    TextInput::make('database.redis.cache.username')
                        ->label(__('setting.cache_redis_username'))
                        ->string(),

                    TextInput::make('database.redis.cache.password')
                        ->label(__('setting.cache_redis_password'))
                        ->string()
                        ->password()
                        ->revealable(),
                ]);
    }

    private function dynamodbSection(): Section
    {
        return

            Section::make(__('setting.cache_dynamodb'))
                ->description(__('setting.cache_dynamodb_description'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::DynamoDB->value)
                ->schema([
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
                ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_cache')
                ->label(__('setting.cache_button_test'))
                ->visible(false)
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::put('test', 'test', now()->addMinute(5));

                        Notification::make('cache_test_success')
                            ->title(__('setting.cache_test_success'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error($e);

                        Notification::make('cache_test_failed')
                            ->title(__('setting.cache_test_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('flush_cache')
                ->label(__('setting.cache_button_flush'))
                ->requiresConfirmation()
                ->color('warning')
                ->disabled(config('app.demo'))
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::flush();

                        Notification::make('cache_flushed')
                            ->title(__('setting.cache_flushed'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.cache');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_cache');
    }

    public function mount(): void
    {
        $this->form->fill([
            'cache' => config('cache'),
            'database' => [
                'redis' => config('database.redis'),
            ],

            'isButtonDisabled' => config('app.demo'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            $this->driverSection(),
            $this->memcachedSection(),
            $this->redisSection(),
            $this->dynamodbSection(),
        ])
            ->disabled(config('app.demo'));
    }

    public function update(): void
    {
        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make('setting_updated')
                ->title(__('setting.cache_updated'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error($e);

            Notification::make('setting_not_updated')
                ->title(__('setting.cache_not_updated'))
                ->danger()
                ->send();
        }
    }
}

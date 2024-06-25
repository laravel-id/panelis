<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\CacheDriver;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
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
            Section::make(__('setting.cache'))
                ->description(__('setting.cache_section_description'))
                ->schema([
                    Radio::make('cache.default')
                        ->label(__('setting.cache_driver'))
                        ->options(CacheDriver::options())
                        ->descriptions(CacheDriver::descriptions())
                        ->live()
                        ->required()
                        ->enum(CacheDriver::class),
                ]),

            Section::make(__('setting.cache_memcached'))
                ->description(__('setting.cache_memcached_description'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Memcached->value)
                ->schema(Settings\Forms\Cache\MemcachedForm::schema()),

            Section::make(__('setting.cache_redis'))
                ->description(__('setting.cache_redis_section_description'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Redis->value)
                ->schema(Settings\Forms\Cache\RedisForm::schema()),

            Section::make(__('setting.cache_dynamodb'))
                ->description(__('setting.cache_dynamodb_section_description'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::DynamoDB->value)
                ->schema(Settings\Forms\Cache\DynamoDBForm::schema()),
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

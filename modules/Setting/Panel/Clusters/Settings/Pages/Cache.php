<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\CacheDriver;
use Modules\Setting\Panel\Clusters\Settings\Enums\CachePermission;
use Modules\Setting\Panel\Clusters\Settings\Forms\Cache\DynamoDBForm;
use Modules\Setting\Panel\Clusters\Settings\Forms\Cache\MemcachedForm;
use Modules\Setting\Panel\Clusters\Settings\Forms\Cache\RedisForm;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;

class Cache extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 70;

    public array $cache;

    public array $database;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_cache')
                ->label(__('setting::setting.cache.btn.test'))
                ->visible(false)
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::put('test', 'test', now()->addMinute(5));

                        Notification::make('cache.test_success')
                            ->title(__('setting::setting.cache.test_success'))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('cache.test_failed')
                            ->title(__('setting::setting.cache.test_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('flush_cache')
                ->label(__('setting::setting.cache.btn.flush'))
                ->requiresConfirmation()
                ->color('warning')
                ->hidden(user_cannot(CachePermission::Flush))
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::flush();

                        Notification::make('cache.flushed')
                            ->title(__('setting::setting.cache.flushed'))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);
                    }
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.cache.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.cache.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(CachePermission::Browse);
    }

    public function updatePermission(): BackedEnum
    {
        return CachePermission::Edit;
    }

    public function mount(): void
    {
        $this->form->fill([
            'cache' => config('cache'),
            'database' => [
                'redis' => config('database.redis'),
            ],

            'isButtonDisabled' => user_cannot(CachePermission::Browse),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(user_cannot(CachePermission::Edit))
            ->schema([
                Section::make(__('setting::setting.cache.label'))
                    ->description(__('setting::setting.cache.section_description'))
                    ->schema([
                        Radio::make('cache.default')
                            ->label(__('setting::setting.cache.driver'))
                            ->options(CacheDriver::class)
                            ->live()
                            ->required()
                            ->enum(CacheDriver::class),
                    ]),

                Section::make(__('setting::setting.cache.memcached.label'))
                    ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Memcached)
                    ->schema(MemcachedForm::schema()),

                Section::make(__('setting::setting.cache.redis.label'))
                    ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Redis)
                    ->schema(RedisForm::schema()),

                Section::make(__('setting::setting.cache.dynamodb.label'))
                    ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::DynamoDB)
                    ->disabled(! CacheDriver::DynamoDB->isInstalled())
                    ->schema(DynamoDBForm::schema()),
            ]);
    }
}

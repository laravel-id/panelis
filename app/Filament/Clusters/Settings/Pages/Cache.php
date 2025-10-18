<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\CacheDriver;
use App\Filament\Clusters\Settings\Enums\CachePermission;
use App\Filament\Clusters\Settings\Forms\Cache\DynamoDBForm;
use App\Filament\Clusters\Settings\Forms\Cache\MemcachedForm;
use App\Filament\Clusters\Settings\Forms\Cache\RedisForm;
use App\Filament\Clusters\Settings\HasUpdateableForm;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Cache extends Page implements HasForms, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ServerStack;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 7;

    public array $cache;

    public array $database;

    public static function canAccess(): bool
    {
        return user_can(CachePermission::Browse);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_cache')
                ->label(__('setting.cache.btn.test'))
                ->visible(false)
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::put('test', 'test', now()->addMinute(5));

                        Notification::make('cache.test_success')
                            ->title(__('setting.cache.test_success'))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('cache.test_failed')
                            ->title(__('setting.cache.test_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('flush_cache')
                ->label(__('setting.cache.btn.flush'))
                ->requiresConfirmation()
                ->color('warning')
                ->hidden(user_cannot(CachePermission::Flush))
                ->action(function (): void {
                    try {
                        \Illuminate\Support\Facades\Cache::flush();

                        Notification::make('cache.flushed')
                            ->title(__('setting.cache.flushed'))
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
        return __('setting.cache.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.cache.navigation');
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
        return $schema->schema([
            Section::make(__('setting.cache.label'))
                ->description(__('setting.cache.section_description'))
                ->schema([
                    Radio::make('cache.default')
                        ->label(__('setting.cache.driver'))
                        ->options(CacheDriver::class)
                        ->live()
                        ->required()
                        ->enum(CacheDriver::class),
                ]),

            Section::make(__('setting.cache.memcached_driver'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Memcached)
                ->schema(MemcachedForm::schema()),

            Section::make(__('setting.cache.redis_driver'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::Redis)
                ->schema(RedisForm::schema()),

            Section::make(__('setting.cache.dynamodb_driver'))
                ->visible(fn (Get $get): bool => $get('cache.default') === CacheDriver::DynamoDB)
                ->schema(DynamoDBForm::schema()),
        ])
            ->disabled(user_cannot(CachePermission::Edit));
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(CachePermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make('setting_updated')
                ->title(__('filament-actions::edit.single.notifications.saved.title'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('setting_not_updated')
                ->title(__('setting.cache.not_updated'))
                ->danger()
                ->send();
        }
    }
}

<?php

namespace App\Filament\Clusters\Databases\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Databases;
use App\Filament\Clusters\Databases\Enums\DatabasePermission;
use App\Filament\Clusters\Databases\Forms\AutoBackupForm;
use App\Filament\Clusters\Databases\Forms\CloudBackupForm;
use App\Jobs\Database\UploadToCloud;
use App\Models\Setting;
use App\Services\Database\Contracts\Database as DbContract;
use App\Services\Database\Database;
use App\Services\Database\Enums\DatabaseDriver;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AutoBackup extends Page implements HasSchemas
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected string $view = 'filament.clusters.databases.pages.auto-backup';

    protected static ?string $cluster = Databases::class;

    protected static ?int $navigationSort = 2;

    public array $database;

    public array $filesystems;

    public array $dropbox;

    private ?DbContract $databaseManager = null;

    public bool $isSupported = true;

    protected function getUpdateAction(): Action
    {
        return Action::make('update_setting')
            ->label(__('ui.btn.update'))
            ->disabled(user_cannot(DatabasePermission::Edit))
            ->action('update');
    }

    public static function getNavigationLabel(): string
    {
        return __('database.auto_backup.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('database.auto_backup.label');
    }

    public static function canAccess(): bool
    {
        return user_can(DatabasePermission::Browse);
    }

    public function boot(Database $database): void
    {
        $driver = config('database.default');

        if (! DatabaseDriver::isSupported($driver)) {
            $this->isSupported = false;

            return;
        }

        try {
            $this->databaseManager = $database->driver(config('database.default'));
        } catch (Throwable $e) {
            Log::warning("Database driver [$driver] could not be initialized.", [
                'exception' => $e,
            ]);

            $this->isSupported = false;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backup')
                ->visible(user_can(DatabasePermission::Backup))
                ->label(__('database.btn.backup_now'))
                ->hidden(! $this->isSupported)
                ->schema([
                    Callout::make(__('database.cloud_backup_disabled'))
                        ->description(__('database.cloud_backup_is_disabled'))
                        ->warning()
                        ->visible(! config('database.cloud_backup_enabled', false)),

                    Section::make()
                        ->visible(config('database.cloud_backup_enabled', false))
                        ->schema([
                            Toggle::make('upload_to_cloud')
                                ->label(__('database.upload_to_cloud', [
                                    'provider' => __(sprintf('database.cloud_storage_%s', config('database.cloud_storage'))),
                                ])),
                        ]),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    try {
                        $path = $this->databaseManager->backup();

                        // upload to cloud if possible
                        if ($data['upload_to_cloud'] ?? false) {
                            UploadToCloud::dispatch($path);
                        }

                        Notification::make('backup')
                            ->title(__('database.file_created'))
                            ->success()
                            ->send();

                        return;
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('backup_failed')
                            ->title(__('database.file_not_created'))
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }

    public function mount(): void
    {
        if (! $this->isSupported) {
            Setting::where('key', 'database.auto_backup_enabled')->delete();
            config()->set('database.auto_backup_enabled', false);
        }

        $this->form->fill([
            'isButtonDisabled' => ! $this->isSupported || ! user_can(DatabasePermission::Edit),

            'database' => [
                'auto_backup_enabled' => config('database.auto_backup_enabled', false),
                'backup_period' => config('database.backup_period'),
                'backup_time' => config('database.backup_time', '00:00'),
                'backup_max' => config('database.backup_max', 3),

                // cloud backup settings
                'cloud_backup_enabled' => config('database.cloud_backup_enabled', false),
                'cloud_storage' => config('database.cloud_storage'),
            ],
            'filesystems' => [
                'disks' => [
                    'dropbox' => [
                        'token' => config('filesystems.disks.dropbox.token'),
                    ],
                ],
            ],

            'dropbox' => [
                'client_id' => config('dropbox.client_id'),
                'client_secret' => config('dropbox.client_secret'),
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $driver = DatabaseDriver::tryFrom(config('database.default'));

        return $schema
            ->components([
                Callout::make(__('database.not_supported'))
                    ->description(__('database.not_supported_reason', ['driver' => $driver?->getLabel()]))
                    ->hidden($this->isSupported)
                    ->warning(),

                Section::make(__('database.auto_backup.label'))
                    ->description(__('database.auto_backup.section_description'))
                    ->hidden(! $this->isSupported)
                    ->schema(AutoBackupForm::schema($this->databaseManager)),

                Section::make(__('database.cloud_backup'))
                    ->description(__('database.cloud_backup_section_description'))
                    ->collapsible()
                    ->hidden(! $this->isSupported)
                    ->visible(function (Get $get): bool {
                        return user_can(DatabasePermission::Backup) && $get('database.auto_backup_enabled');
                    })
                    ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled') || config('panelis.demo', false))
                    ->schema(CloudBackupForm::schema()),
            ])
            ->disabled(user_cannot(DatabasePermission::Edit));
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(DatabasePermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('backup_updated')
                ->title(__('database.backup_updated'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('backup_not_updated')
                ->title(__('database.backup_not_updated'))
                ->danger()
                ->send();
        }
    }
}

<?php

namespace App\Filament\Clusters\Databases\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Databases;
use App\Filament\Clusters\Databases\Enums\DatabasePermission;
use App\Filament\Clusters\Databases\Forms\AutoBackupForm;
use App\Filament\Clusters\Databases\Forms\CloudBackupForm;
use App\Jobs\Database\UploadToCloud;
use App\Models\Setting;
use App\Services\Database\DatabaseFactory;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
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

class AutoBackup extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected string $view = 'filament.clusters.databases.pages.auto-backup';

    protected static ?string $cluster = Databases::class;

    protected static ?int $navigationSort = 2;

    public array $database;

    public array $filesystems;

    public array $dropbox;

    private DatabaseFactory $databaseService;

    protected function getUpdateAction(): Action
    {
        return Action::make('update_setting')
            ->label(__('ui.btn.update'))
            ->color('primary')
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

    public function boot(?DatabaseFactory $database): void
    {
        $this->databaseService = $database;
        $this->databaseService->driver(config('database.default'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backup')
                ->visible(user_can(DatabasePermission::Backup))
                ->label(__('database.btn.backup_now'))
                ->schema([
                    //                    AlertBox::make('cloud_backup_disabled')
                    //                        ->label(__('database.cloud_backup_disabled'))
                    //                        ->helperText(__('database.cloud_backup_is_disabled'))
                    //                        ->visible(! config('database.cloud_backup_enabled', false))
                    //                        ->warning(),

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
                        $path = $this->databaseService->backup();

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
        if (! $this->databaseService->isAvailable()) {
            Setting::where('key', 'database.auto_backup_enabled')->delete();
            config()->set('database.auto_backup_enabled', false);
        }

        $this->form->fill([
            'isButtonDisabled' => ! $this->databaseService->isAvailable() || ! user_can(DatabasePermission::Edit),
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
        return $schema
            ->components([
                //                AlertBox::make('package_installed')
                //                    ->warning()
                //                    ->label(__('database.auto_backup_is_disabled'))
                //                    ->helperText(__('database.auto_backup_disabled_reason'))
                //                    ->hidden(fn (): bool => $this->databaseService->isAvailable() ?? false),

                Section::make(__('database.auto_backup.label'))
                    ->description(__('database.auto_backup.section_description'))
                    ->schema(AutoBackupForm::schema($this->databaseService)),

                Section::make(__('database.cloud_backup'))
                    ->description(__('database.cloud_backup_section_description'))
                    ->collapsible()
                    ->visible(function (Get $get): bool {
                        return user_can(DatabasePermission::Backup) && $get('database.auto_backup_enabled');
                    })
                    ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled') || config('app.demo'))
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

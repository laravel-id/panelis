<?php

namespace App\Filament\Clusters\Databases\Pages;

use App\Filament\Clusters\Databases;
use App\Models\Enums\DatabasePeriod;
use App\Models\Enums\DatabaseType;
use App\Models\Setting;
use App\Services\Database\Database;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class AutoBackup extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.clusters.databases.pages.auto-backup';

    protected static ?string $cluster = Databases::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('navigation.auto_backup');
    }

    public function getTitle(): string|Htmlable
    {
        return __('database.auto_backup');
    }

    public array $database;

    public bool $isButtonDisabled = true;

    private ?Database $databaseService;

    public function boot(?Database $database): void
    {
        $this->databaseService = $database;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backup')
                ->label(__('database.button_backup_manually'))
                ->requiresConfirmation()
                ->action(function (): void {
                    try {
                        $this->databaseService->backup();

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
        if (! $this->databaseService?->isAvailable()) {
            Setting::where('key', 'database.auto_backup_enabled')->delete();
            config()->set('database.auto_backup_enabled', false);
        }

        $this->form->fill([
            'isButtonDisabled' => ! $this->databaseService?->isAvailable(),
            'database' => [
                'auto_backup_enabled' => config('database.auto_backup_enabled', false),
                'backup_period' => config('database.backup_period'),
                'backup_time' => config('database.backup_time', '00:00'),
                'backup_max' => config('database.backup_max', 3),
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        $database = data_get(config('database.connections'), config('database.default'));

        return $form->schema([
            AlertBox::make('package_installed')
                ->warning()
                ->label(__('database.auto_backup_is_disabled'))
                ->helperText(__('database.auto_backup_disabled_reason'))
                ->hidden(fn (): bool => $this->databaseService?->isAvailable() ?? false),

            Section::make(__('database.auto_backup'))
                ->description(__('database.auto_backup_info'))
                ->schema([
                    Placeholder::make('database.default')
                        ->label(__('database.type'))
                        ->content(DatabaseType::getType(config('database.default'))),

                    Placeholder::make('database.version')
                        ->label(__('database.version'))
                        ->content($this->databaseService?->getVersion()),

                    Placeholder::make('database.url')
                        ->label(__('database.path'))
                        ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                        ->content($database['database'] ?? null),

                    Toggle::make('database.auto_backup_enabled')
                        ->label(__('database.backup_enabled'))
                        ->live()
                        ->disabled(fn (): bool => ! $this->databaseService?->isAvailable()),

                    Placeholder::make('database.size')
                        ->label(__('database.size'))
                        ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                        ->content(function () use ($database): ?string {
                            if (config('database.default') === DatabaseType::SQLite->value) {
                                return Number::fileSize(File::size($database['database']));
                            }

                            return null;
                        })
                        ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

                    Radio::make('database.backup_period')
                        ->label(__('database.period'))
                        ->options(DatabasePeriod::options())
                        ->required()
                        ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

                    TimePicker::make('database.backup_time')
                        ->label(__('database.backup_time'))
                        ->seconds(false)
                        ->timezone(config('app.timezone'))
                        ->native(false)
                        ->required()
                        ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

                    TextInput::make('database.backup_max')
                        ->label(__('database.backup_max'))
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),
                ]),
        ]);
    }

    public function update(): void
    {
        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

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

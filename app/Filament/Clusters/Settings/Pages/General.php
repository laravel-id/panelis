<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Actions\Setting\ExportAll;
use App\Actions\Setting\ImportAll;
use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\SettingPermission;
use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class General extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public array $app;

    public array $telescope;

    public bool $isButtonDisabled = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label(__('ui.btn.export'))
                ->visible(user_can(SettingPermission::Export))
                ->requiresConfirmation()
                ->action(function (): StreamedResponse {
                    return ExportAll::run();
                }),

            ActionGroup::make([
                Action::make('import')
                    ->label(__('ui.btn.import'))
                    ->visible(user_can(SettingPermission::Import))
                    ->requiresConfirmation()
                    ->form([
                        FileUpload::make('settings')
                            ->label(__('setting.general.exported_file'))
                            ->previewable(false)
                            ->storeFiles(false)
                            ->fetchFileInformation(false)
                            ->disk('local')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/json',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        try {
                            ImportAll::run($data['settings']);

                            Notification::make('setting_imported')
                                ->title(__('setting.general.setting_imported'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Logger::error($e);

                            Notification::make('setting_not_imported')
                                ->title(__('setting.general.setting_not_imported'))
                                ->danger()
                                ->send();
                        }
                    }),
            ]),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.general.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.general.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(SettingPermission::Browse);
    }

    public function mount(): void
    {
        $this->form->fill([
            'app' => [
                'url' => config('app.url'),
                'debug' => config('app.debug'),
                'name' => config('app.name'),
                'description' => config('app.description'),
                'locales' => config('app.locales', [config('app.locale')]),
                'locale' => Setting::get('app.locale', config('app.locale')),
                'email' => config('app.email'),
                'email_as_sender' => config('app.email_as_sender'),
            ],

            'telescope' => [
                'enabled' => config('telescope.enabled', false),
            ],

            'isButtonDisabled' => user_cannot(SettingPermission::Edit),
        ]);
    }

    public function form(Form $form): Form
    {
        $locales = collect(config('app.locales'))
            ->mapWithKeys(function ($locale): array {
                return [$locale => LanguageSwitch::make()->getLabel($locale)];
            })
            ->toArray();

        // no locales in database setting
        // use default locale from Laravel
        if (empty($locales)) {
            $locales[config('app.locale')] = LanguageSwitch::make()->getLabel(config('app.locale'));
        }

        return $form->schema([
            Section::make(__('setting.general.label'))
                ->description(__('setting.general.section_description'))
                ->schema(Settings\Forms\General\GeneralForm::make()),

            Section::make(__('setting.general.debug_mode'))
                ->collapsed()
                ->schema(Settings\Forms\General\DebugForm::make()),
        ])->disabled(user_cannot(SettingPermission::Edit));
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(SettingPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        $state = $this->form->getState();
        foreach ($state['app'] as $key => $value) {
            $key = sprintf('app.%s', $key);
            if ($value === false) {
                $value = '0';
            }

            if ($key === 'app.email_as_sender' && data_get($state, 'app.email_as_sender') === true) {
                Setting::set('mail.from.address', data_get($state, 'app.email'));
            }

            Setting::set($key, $value);
        }

        // specific setting for telescope
        Setting::set('telescope.enabled', data_get($state, 'telescope.enabled', false));

        event(new SettingUpdated);

        Notification::make('general_updated')
            ->title(__('filament-actions::edit.single.notifications.saved.title'))
            ->success()
            ->send();
    }
}

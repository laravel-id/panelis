<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Validation\ValidationException;
use Modules\Setting\Actions\ExportAll;
use Modules\Setting\Actions\ImportAll;
use Modules\Setting\Events\SettingUpdated;
use Modules\Setting\Models\Setting;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\SettingPermission;
use Modules\Setting\Panel\Clusters\Settings\Forms\General\DebugForm;
use Modules\Setting\Panel\Clusters\Settings\Forms\General\GeneralForm;
use Modules\Setting\Panel\Clusters\Settings\Forms\General\ImageForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class General extends Page
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 10;

    public array $app;

    public array $telescope;

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
                    ->schema([
                        FileUpload::make('settings')
                            ->label(__('setting::setting.general.exported_file'))
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
                                ->title(__('setting::setting.general.setting_imported'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Logger::error($e);

                            Notification::make('setting_not_imported')
                                ->title(__('setting::setting.general.setting_not_imported'))
                                ->danger()
                                ->send();
                        }
                    }),
            ]),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.general.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.general.navigation');
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
                'logo' => config('app.logo'),
                'use_logo_in_panel' => config('app.use_logo_in_panel'),
                'favicon' => config('app.favicon'),
            ],

            'telescope' => [
                'enabled' => config('telescope.enabled', false),
            ],

            'isButtonDisabled' => user_cannot(SettingPermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
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

        return $schema->components([
            Section::make(__('setting::setting.general.image'))
                ->columns(2)
                ->description(__('setting::setting.general.section_image'))
                ->collapsible()
                ->schema(ImageForm::schema()),

            Section::make(__('setting::setting.general.label'))
                ->description(__('setting::setting.general.section_description'))
                ->schema(GeneralForm::schema()),

            Section::make(__('setting::setting.general.debug_mode'))
                ->collapsed()
                ->schema(DebugForm::schema()),
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

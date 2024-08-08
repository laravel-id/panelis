<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class General extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public array $app;

    public array $telescope;

    public bool $isButtonDisabled = false;

    public function getTitle(): string|Htmlable
    {
        return __('setting.general');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_general');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->can('ViewGeneralSetting');
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

            'isButtonDisabled' => ! Auth::user()->can('UpdateGeneralSetting'),
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
            Section::make(__('setting.general'))
                ->description(__('setting.general_section_description'))
                ->schema(Settings\Forms\General\GeneralForm::make()),

            Section::make(__('setting.debug_mode'))
                ->collapsed()
                ->schema(Settings\Forms\General\DebugForm::make()),
        ])->disabled(! Auth::user()->can('UpdateGeneralSetting'));
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(Auth::user()->can('UpdateGeneralSetting'), Response::HTTP_FORBIDDEN);

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
            ->title(__('setting.general_updated'))
            ->success()
            ->send();
    }
}

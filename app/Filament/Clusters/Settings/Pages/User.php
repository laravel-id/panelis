<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\Role;
use App\Models\Setting;
use Exception;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class User extends Page implements HasForms, Settings\HasUpdateableForm
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public ?array $user;

    public bool $isButtonDisabled = false;

    public function getTitle(): string|Htmlable
    {
        return __('setting.user');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_user');
    }

    public function mount(): void
    {
        $this->form->fill([
            'user' => config('user'),
        ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->can('ViewUserSetting');
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(! Auth::user()->can('UpdateUserSetting'))
            ->schema([
                Section::make(__('setting.user'))
                    ->description(__('setting.user_section_description'))
                    ->schema([
                        Select::make('user.default_role')
                            ->label(__('setting.user_default_role'))
                            ->native(false)
                            ->searchable()
                            ->options(Role::options())
                            ->required(),

                        Radio::make('user.avatar_provider')
                            ->label(__('setting.user_avatar_provider'))
                            ->options(Settings\Enums\AvatarProvider::options())
                            ->required(),
                    ]),
            ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(Auth::user()->can('UpdateUserSetting'), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make('user_updated')
                ->title(__('setting.user_updated'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('user_not_updated')
                ->title(__('setting.user_not_updated'))
                ->danger()
                ->send();
        }
    }
}

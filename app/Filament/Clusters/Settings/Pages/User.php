<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\AvatarProvider;
use App\Filament\Clusters\Settings\Enums\LibravatarStyle;
use App\Filament\Clusters\Settings\Enums\UserPermission;
use App\Models\Role;
use App\Models\Setting;
use Exception;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
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
        return __('setting.user.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.user.navigation');
    }

    public function mount(): void
    {
        $this->form->fill([
            'user' => config('user'),
        ]);
    }

    public static function canAccess(): bool
    {
        return user_can(UserPermission::Browse);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(user_cannot(UserPermission::Edit))
            ->schema([
                Section::make(__('setting.user.label'))
                    ->description(__('setting.user.section_description'))
                    ->schema([
                        Select::make('user.default_role')
                            ->label(__('setting.user.default_role'))
                            ->native(false)
                            ->searchable()
                            ->options(Role::options())
                            ->required(),

                        Radio::make('user.avatar_provider')
                            ->label(__('setting.user.avatar_provider'))
                            ->options(AvatarProvider::options())
                            ->live()
                            ->required(),

                        Radio::make('user.avatar_libravatar_style')
                            ->label(__('setting.user.avatar_libravatar_style'))
                            ->visible(fn (Get $get): bool => $get('user.avatar_provider') === AvatarProvider::Libravatar->value)
                            ->live()
                            ->enum(LibravatarStyle::class)
                            ->required(fn (Get $get): bool => $get('user.avatar_provider') === AvatarProvider::Libravatar->value)
                            ->options(LibravatarStyle::options()),

                        Placeholder::make('avatar')
                            ->hiddenLabel()
                            ->label('setting.user.sample_avatar')
                            ->content(function (Get $get): HtmlString {
                                $provider = AvatarProvider::tryFrom($get('user.avatar_provider')) ?? AvatarProvider::UIAvatars;
                                $style = $get('user.avatar_libravatar_style');

                                if ($provider === AvatarProvider::UIAvatars) {
                                    $image = 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name);
                                }

                                return new HtmlString(sprintf('<img src="%s"/>', $image ?? $provider->getImageUrl(Auth::user(), $style)));
                            }),
                    ]),
            ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(UserPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make('user_updated')
                ->title(__('filament-actions::edit.single.notifications.saved.title'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Log::error($e);

            Notification::make('user_not_updated')
                ->title(__('setting.user.not_updated'))
                ->danger()
                ->send();
        }
    }
}

<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\AvatarProvider;
use App\Filament\Clusters\Settings\Enums\LibravatarStyle;
use App\Filament\Clusters\Settings\Enums\UserPermission;
use App\Filament\Clusters\Settings\HasUpdateableForm;
use App\Models\Role;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class User extends Page implements HasForms, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::UserCircle;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public ?array $user;

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
            'isButtonDisabled' => user_cannot(UserPermission::Edit),
        ]);
    }

    public static function canAccess(): bool
    {
        return user_can(UserPermission::Browse);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(user_cannot(UserPermission::Edit))
            ->components([
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
                            ->options(AvatarProvider::class)
                            ->enum(AvatarProvider::class)
                            ->default(AvatarProvider::UIAvatars)
                            ->live()
                            ->required(),

                        Radio::make('user.avatar_libravatar_style')
                            ->label(__('setting.user.avatar_libravatar_style'))
                            ->visible(fn (Get $get): bool => $get('user.avatar_provider') === AvatarProvider::Libravatar)
                            ->live()
                            ->enum(LibravatarStyle::class)
                            ->required(fn (Get $get): bool => $get('user.avatar_provider') === AvatarProvider::Libravatar)
                            ->options(LibravatarStyle::class),

                        Image::make(
                            url: function (Get $get): ?string {
                                $provider = $get('user.avatar_provider') ?? AvatarProvider::UIAvatars;
                                $style = $get('user.avatar_libravatar_style');

                                return $provider->getImageUrl(Auth::user(), $style);
                            },
                            alt: 'Avatar',
                        ),
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

            event(new SettingUpdated);

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

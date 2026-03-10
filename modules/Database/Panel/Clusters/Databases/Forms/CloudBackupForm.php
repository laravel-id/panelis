<?php

namespace Modules\Database\Panel\Clusters\Databases\Forms;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\TextSize;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Socialite\Facades\Socialite;
use Modules\Database\Panel\Clusters\Databases\Enums\CloudProvider;
use Modules\Setting\Events\SettingUpdated;
use Modules\Setting\Models\Setting;
use SocialiteProviders\Manager\OAuth2\User;

class CloudBackupForm
{
    public static function schema(): array
    {
        $activeDriver = Setting::get('database.cloud_storage');

        return [
            Toggle::make('database.cloud_backup_enabled')
                ->label(__('database::database.cloud_backup_enabled'))
                ->afterStateUpdated(function (Set $set, ?bool $state): void {
                    $set('database.cloud_storage', null);

                    Setting::set('database.cloud_backup_enabled', $state);
                    event(new SettingUpdated);
                })
                ->live(),

            Radio::make('database.cloud_storage')
                ->label(__('database::database.cloud_storage'))
                ->options(CloudProvider::class)
                ->afterStateUpdated(function (CloudProvider $state): void {
                    $driver = $state->value;
                    if (! empty($driver)) {
                        Setting::set('database.cloud_storage', $driver);

                        // set default callback URI
                        Setting::set(sprintf('services.%s.redirect', $driver), route('panelis.database.callback'));

                        event(new SettingUpdated);
                    }
                })
                ->live()
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->enum(CloudProvider::class)
                ->required(fn (Get $get): bool => $get('database.cloud_backup_enabled')),

            Callout::make(fn (Get $get): ?string => __(sprintf('database::database.%s.no_package_title', $get('database.cloud_storage')->value)))
                ->description(fn (Get $get): ?string => __(sprintf('database::database.%s.no_package_description', $get('database.cloud_storage')->value)))
                ->warning()
                ->visible(function (Get $get): bool {
                    $driver = $get('database.cloud_storage');
                    if (empty($driver)) {
                        return false;
                    }

                    return $get('database.cloud_backup_enabled') && ! $driver->isInstalled();
                }),

            TextInput::make(sprintf('services.%s.client_id', $activeDriver))
                ->label(__('database::database.client_id'))
                ->hint(function (Get $get): ?Htmlable {
                    $driver = $get('database.cloud_storage');
                    if (empty($driver)) {
                        return null;
                    }

                    return str(__(sprintf('database::database.%s.doc_hint', $driver->value), ['driver' => $driver->getLabel()]))
                        ->inlineMarkdown()
                        ->toHtmlString();
                })
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->afterStateUpdated(function (?string $state, Get $get): void {
                    if (! empty($state)) {
                        $driver = $get('database.cloud_storage')?->value;
                        if (! empty($driver)) {
                            Setting::set(sprintf('services.%s.client_id', $driver), $state);
                            event(new SettingUpdated);
                        }
                    }
                })
                ->live(onBlur: true)
                ->password()
                ->revealable()
                ->required(),

            TextInput::make(sprintf('services.%s.client_secret', $activeDriver))
                ->label(__('database::database.client_secret'))
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->afterStateUpdated(function (?string $state, Get $get): void {
                    if (! empty($state)) {
                        $driver = $get('database.cloud_storage')?->value;

                        if (! empty($driver)) {
                            Setting::set(sprintf('services.%s.client_secret', $driver), $state);
                            event(new SettingUpdated);
                        }
                    }
                })
                ->live(onBlur: true)
                ->password()
                ->revealable()
                ->required(),

            TextEntry::make('redirect_uri')
                ->label(__('database::database.redirect_uri'))
                ->state(route('panelis.database.callback'))
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->size(TextSize::Medium)
                ->copyable(),

            self::dropboxAction(),
        ];
    }

    private static function dropboxAction(): Actions
    {
        $user = null;
        $driver = config('database.cloud_storage');
        $token = config(sprintf('filesystems.disks.%s.token', $driver));

        if (! empty($token) && CloudProvider::tryFrom($driver)->isInstalled()) {
            /**
             * @var User $user
             */
            $user = Socialite::driver($driver)->userFromToken($token);
        }

        return Actions::make([
            Action::make('authorize_dropbox')
                ->label(function (Get $get): string {
                    $driver = $get('database.cloud_storage');

                    return __('database::database.btn.authorize', ['driver' => $driver->getLabel()]);
                })
                ->disabled(config('panelis.demo', false))
                ->visible(empty($user))
                ->disabled(function (Get $get): bool {
                    $driver = $get('database.cloud_storage');
                    if (empty($driver)) {
                        return true;
                    }

                    if (! $driver->isInstalled()) {
                        return true;
                    }

                    return empty($get(sprintf('services.%s.client_id', $driver->value)))
                        || empty($get(sprintf('services.%s.client_secret', $driver->value)));
                })
                ->url(function (Get $get): string {
                    $driver = $get('database.cloud_storage');
                    $states = [
                        'scopes' => $driver->getScopes(),
                    ];

                    return route('panelis.database.redirect', $states);
                }),

            Action::make('revoke')
                ->label(__('database::database.btn.revoke', ['name' => $user?->getName()]))
                ->disabled(config('panelis.demo', false))
                ->visible(! empty($user))
                ->requiresConfirmation()
                ->action(function (Set $set): void {
                    $driver = config('database.cloud_storage');

                    Setting::set(sprintf('services.%s.client_id', $driver), null);
                    Setting::set(sprintf('services.%s.client_secret', $driver), null);
                    Setting::set(sprintf('services.%s.refresh_token', $driver), null);
                    Setting::set(sprintf('services.%s.expires_in', $driver), null);

                    Setting::set('filesystems.disks.dropbox.token', null);

                    Setting::set('database.cloud_storage', null);
                    Setting::set('database.cloud_backup_enabled', false);
                    event(new SettingUpdated);

                    $set('database.cloud_backup_enabled', false);
                    $set('database.cloud_storage', null);
                    $set(sprintf('database.%s.client_id', $driver), null);
                    $set(sprintf('database.%s.client_secret', $driver), null);
                }),
        ])->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox);
    }
}

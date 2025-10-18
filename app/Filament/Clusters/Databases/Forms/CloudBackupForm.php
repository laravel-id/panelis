<?php

namespace App\Filament\Clusters\Databases\Forms;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Databases\Enums\CloudProvider;
use App\Filament\Clusters\Databases\Pages\AutoBackup;
use App\Models\Setting;
use App\Services\OAuth\OAuth;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class CloudBackupForm
{
    private static OAuth $oauth;

    public static function make(): array
    {
        self::$oauth = app(OAuth::class)
            ->driver(config('database.cloud_storage', CloudProvider::Dropbox->value));

        return [
            Toggle::make('database.cloud_backup_enabled')
                ->label(__('database.cloud_backup_enabled'))
                ->afterStateUpdated(function (Set $set, ?bool $state): void {
                    $set('database.cloud_storage', null);

                    Setting::set('database.cloud_backup_enabled', $state);
                    event(new SettingUpdated);
                })
                ->live(),

            Radio::make('database.cloud_storage')
                ->label(__('database.cloud_storage'))
                ->options(CloudProvider::options())
                ->afterStateUpdated(function (?string $state): void {
                    if (! empty($state)) {
                        Setting::set('database.cloud_storage', $state);
                        event(new SettingUpdated);

                        Config::set('oauth.provider', $state);
                        self::$oauth = app(OAuth::class)
                            ->driver($state);
                    }
                })
                ->live()
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->enum(CloudProvider::class)
                ->required(fn (Get $get): bool => $get('database.cloud_backup_enabled')),

            TextInput::make('dropbox.client_id')
                ->label(__('database.dropbox_api_key'))
                ->hint(\str(__('database.dropbox_token_hint'))->inlineMarkdown()->toHtmlString())
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox->value)
                ->afterStateUpdated(function (?string $state): void {
                    if (! empty($state)) {
                        Setting::set('dropbox.client_id', $state);
                        event(new SettingUpdated);
                    }
                })
                ->live(onBlur: true)
                ->password()
                ->revealable()
                ->required(),

            TextInput::make('dropbox.client_secret')
                ->label(__('database.dropbox_api_secret'))
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox->value)
                ->afterStateUpdated(function (?string $state): void {
                    if (! empty($state)) {
                        Setting::set('dropbox.client_secret', $state);
                        event(new SettingUpdated);
                    }
                })
                ->live(onBlur: true)
                ->password()
                ->revealable()
                ->required(),

            self::dropboxAction(),
        ];
    }

    private static function dropboxAction(): Actions
    {
        $user = null;
        $token = config('filesystems.disks.dropbox.token');
        if (! empty($token)) {
            $user = self::$oauth->getUser();
        }

        return Actions::make([
            Action::make('authorize_dropbox')
                ->label(__('database.btn_authorize_dropbox'))
                ->disabled(config('app.demo'))
                ->visible(empty($user))
                ->disabled(fn (Get $get): bool => empty($get('dropbox.client_id')) || empty($get('dropbox.client_secret')))
                ->url(function (Get $get): string {
                    $states = [
                        'redirect' => AutoBackup::getUrl(),
                    ];

                    return self::$oauth->setAppKey($get('dropbox.client_id') ?? '')
                        ->setState(Crypt::encryptString(json_encode($states)))
                        ->setRedirectUri(route('callback.dropbox'))
                        ->getAuthorizeUrl();
                }),

            Action::make('revoke_dropbox')
                ->label(__('database.revoke_dropbox', ['name' => $user?->getName()]))
                ->disabled(config('app.demo'))
                ->visible(! empty($user))
                ->requiresConfirmation()
                ->action(function (Set $set): void {
                    Setting::set('dropbox.client_id', null);
                    Setting::set('dropbox.client_secret', null);
                    Setting::set('filesystems.disks.dropbox.token', null);
                    Setting::set('dropbox.refresh_token', null);
                    Setting::set('database.cloud_storage', null);
                    Setting::set('database.cloud_backup_enabled', false);
                    event(new SettingUpdated);

                    $set('database.cloud_backup_enabled', false);
                    $set('database.cloud_storage', null);
                    $set('dropbox.client_id', null);
                    $set('dropbox.client_secret', null);
                }),
        ])->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox->value);
    }
}

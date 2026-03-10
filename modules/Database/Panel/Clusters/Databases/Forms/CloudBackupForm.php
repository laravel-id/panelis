<?php

namespace Modules\Database\Panel\Clusters\Databases\Forms;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Modules\Database\Panel\Clusters\Databases\Enums\CloudProvider;
use Modules\Database\Panel\Clusters\Databases\Pages\AutoBackup;
use Modules\Database\Services\OAuth\OAuth;
use Modules\Setting\Events\SettingUpdated;
use Modules\Setting\Models\Setting;

class CloudBackupForm
{
    private static OAuth $oauth;

    public static function schema(): array
    {
        self::$oauth = app(OAuth::class)
            ->driver(config('database.cloud_storage', CloudProvider::Dropbox->value));

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
                    $provider = $state->value;
                    Setting::set('database.cloud_storage', $provider);
                    event(new SettingUpdated);

                    Config::set('oauth.provider', $provider);
                    static::$oauth = app(OAuth::class)
                        ->driver($provider);
                })
                ->live()
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->enum(CloudProvider::class)
                ->required(fn (Get $get): bool => $get('database.cloud_backup_enabled')),

            TextInput::make('dropbox.client_id')
                ->label(__('database::database.dropbox.api_key'))
                ->hint(\str(__('database::database.dropbox.token_hint'))->inlineMarkdown()->toHtmlString())
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox)
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
                ->label(__('database::database.dropbox.api_secret'))
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox)
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
            $user = static::$oauth->getUser();
        }

        return Actions::make([
            Action::make('authorize_dropbox')
                ->label(__('database::database.dropbox.btn.authorize'))
                ->disabled(config('panelis.demo', false))
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
                ->label(__('database::database.dropbox.btn.revoke', ['name' => $user?->getName()]))
                ->disabled(config('panelis.demo', false))
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
        ])->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled') && $get('database.cloud_storage') === CloudProvider::Dropbox);
    }
}

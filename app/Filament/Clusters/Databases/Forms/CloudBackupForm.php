<?php

namespace App\Filament\Clusters\Databases\Forms;

use App\Filament\Clusters\Databases\Enums\CloudProvider;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CloudBackupForm
{
    public static function make(): array
    {
        return [
            Toggle::make('database.cloud_backup_enabled')
                ->label(__('database.cloud_backup_enabled'))
                ->afterStateUpdated(function (Set $set): void {
                    $set('database.cloud_storage', null);
                })
                ->live(),

            Radio::make('database.cloud_storage')
                ->label(__('database.cloud_storage'))
                ->options(CloudProvider::options())
                ->live()
                ->visible(fn (Get $get): bool => $get('database.cloud_backup_enabled'))
                ->enum(CloudProvider::class)
                ->required(fn (Get $get): bool => $get('database.cloud_backup_enabled')),

            TextInput::make('filesystems.disks.dropbox.token')
                ->label(__('database.dropbox_token'))
                ->hint(\str(__('database.dropbox_token_hint'))->inlineMarkdown()->toHtmlString())
                ->password()
                ->visible(function (Get $get): bool {
                    $dropbox = $get('database.cloud_storage') === CloudProvider::Dropbox->value;
                    $enabled = $get('database.cloud_backup_enabled');

                    if (empty($dropbox) || ! $enabled) {
                        return false;
                    }

                    return true;
                })
                ->required(fn (Get $get): bool => $get('database.cloud_storage') === CloudProvider::Dropbox->value),
        ];
    }
}

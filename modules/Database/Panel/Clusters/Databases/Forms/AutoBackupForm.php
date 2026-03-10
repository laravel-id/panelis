<?php

namespace Modules\Database\Panel\Clusters\Databases\Forms;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;
use Modules\Database\Panel\Clusters\Databases\Enums\DatabasePeriod;
use Modules\Database\Services\Database\Contracts\Database;
use Modules\Database\Services\Database\Enums\DatabaseDriver;

class AutoBackupForm
{
    public static function schema(?Database $databaseManager): array
    {
        if (empty($databaseManager)) {
            return [];
        }

        $database = data_get(config('database.connections'), config('database.default'));

        return [
            TextEntry::make('database.default')
                ->label(__('database::database.type'))
                ->state(DatabaseDriver::tryFrom(config('database.default'))?->getLabel()),

            TextEntry::make('database.version')
                ->label(__('database::database.version'))
                ->state($databaseManager->getVersion()),

            TextEntry::make('database.url')
                ->label(__('database::database.path'))
                ->visible(fn (): bool => config('database.default') === DatabaseDriver::SQLite->value)
                ->helperText(function (): ?string {
                    if (config('panelis.demo', false)) {
                        return __('database::database.hidden_in_demo');
                    }

                    return null;
                })
                ->state(config('panelis.demo', false) ? '***' : $database['database'] ?? null),

            Toggle::make('database.auto_backup_enabled')
                ->label(__('database::database.backup_enabled'))
                ->live()
                ->disabled(fn (): bool => ! $databaseManager?->isAvailable()),

            TextEntry::make('database.size')
                ->label(__('database::database.size'))
                ->visible(fn (): bool => config('database.default') === DatabaseDriver::SQLite->value)
                ->state(function () use ($database): ?string {
                    if (config('database.default') === DatabaseDriver::SQLite->value) {
                        return Number::fileSize(File::size($database['database']));
                    }

                    return null;
                })
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            Radio::make('database.backup_period')
                ->label(__('database::database.period'))
                ->options(DatabasePeriod::options())
                ->required(fn (Get $get): bool => $get('database.auto_backup_enabled'))
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            TimePicker::make('database.backup_time')
                ->label(__('database::database.backup_time'))
                ->seconds(false)
                ->timezone(config('app.timezone'))
                ->native(false)
                ->required()
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            TextInput::make('database.backup_max')
                ->label(__('database::database.backup_max'))
                ->numeric()
                ->minValue(1)
                ->required()
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),
        ];
    }
}

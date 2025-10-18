<?php

namespace App\Filament\Clusters\Databases\Forms;

use App\Filament\Clusters\Databases\Enums\DatabasePeriod;
use App\Filament\Clusters\Databases\Enums\DatabaseType;
use App\Services\Database\DatabaseFactory;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;

class AutoBackupForm
{
    public static function schema(?DatabaseFactory $databaseService): array
    {
        $database = data_get(config('database.connections'), config('database.default'));

        return [
            TextEntry::make('database.default')
                ->label(__('database.type'))
                ->state(DatabaseType::getType(config('database.default'))),

            TextEntry::make('database.version')
                ->label(__('database.version'))
                ->state($databaseService?->getVersion()),

            TextEntry::make('database.url')
                ->label(__('database.path'))
                ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                ->helperText(function (): ?string {
                    if (config('app.demo')) {
                        return __('database.hidden_in_demo');
                    }

                    return null;
                })
                ->state(config('app.demo') ? '***' : $database['database'] ?? null),

            Toggle::make('database.auto_backup_enabled')
                ->label(__('database.backup_enabled'))
                ->live()
                ->disabled(fn (): bool => ! $databaseService?->isAvailable()),

            TextEntry::make('database.size')
                ->label(__('database.size'))
                ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                ->state(function () use ($database): ?string {
                    if (config('database.default') === DatabaseType::SQLite->value) {
                        return Number::fileSize(File::size($database['database']));
                    }

                    return null;
                })
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            Radio::make('database.backup_period')
                ->label(__('database.period'))
                ->options(DatabasePeriod::options())
                ->required(fn (Get $get): bool => $get('database.auto_backup_enabled'))
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            TimePicker::make('database.backup_time')
                ->label(__('database.backup_time'))
                ->seconds(false)
                ->timezone(config('app.timezone'))
                ->native(false)
                ->required()
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),

            TextInput::make('database.backup_max')
                ->label(__('database.backup_max'))
                ->numeric()
                ->minValue(1)
                ->required()
                ->disabled(fn (Get $get): bool => ! $get('database.auto_backup_enabled')),
        ];
    }
}

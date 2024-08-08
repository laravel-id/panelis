<?php

namespace App\Filament\Clusters\Databases\Forms;

use App\Filament\Clusters\Databases\Enums\DatabasePeriod;
use App\Filament\Clusters\Databases\Enums\DatabaseType;
use App\Services\Database\Database;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;

class AutoBackupForm
{
    public static function make(?Database $databaseService): array
    {
        $database = data_get(config('database.connections'), config('database.default'));

        return [
            Placeholder::make('database.default')
                ->label(__('database.type'))
                ->content(DatabaseType::getType(config('database.default'))),

            Placeholder::make('database.version')
                ->label(__('database.version'))
                ->content($databaseService?->getVersion()),

            Placeholder::make('database.url')
                ->label(__('database.path'))
                ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                ->helperText(function (): ?string {
                    if (config('app.demo')) {
                        return __('database.hidden_in_demo');
                    }

                    return null;
                })
                ->content(config('app.demo') ? '***' : $database['database'] ?? null),

            Toggle::make('database.auto_backup_enabled')
                ->label(__('database.backup_enabled'))
                ->live()
                ->disabled(fn (): bool => ! $databaseService?->isAvailable()),

            Placeholder::make('database.size')
                ->label(__('database.size'))
                ->visible(fn (): bool => config('database.default') === DatabaseType::SQLite->value)
                ->content(function () use ($database): ?string {
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

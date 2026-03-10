<?php

namespace Modules\Module\Panel\Resources;

use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Modules\Module\Models\Module;
use Modules\Module\Panel\Resources\Resources\ModuleResource\Enums\ModulePermission;
use Modules\Module\Panel\Resources\Resources\ModuleResource\Pages\ListModules;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('module::module.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('module::module.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('ui.system');
    }

    public static function canAccess(): bool
    {
        return user_can(ModulePermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_enabled')
                    ->label(__('module::module.is_active'))
                    ->disabled(fn (Module $record): bool => $record->is_builtin || user_cannot(ModulePermission::Edit))
                    ->afterStateUpdated(function () {
                        Cache::forget('modules');
                    }),

                TextColumn::make('name')
                    ->label(__('module::module.name'))
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('module::module.description'))
                    ->searchable(),
            ])
            ->filters([
                TernaryFilter::make('is_enabled')
                    ->label(__('module::module.is_active')),
            ])
            ->recordActions([
                DeleteAction::make('delete')
                    ->disabled(fn (Module $record): bool => $record->is_builtin)
                    ->visible(user_can(ModulePermission::Delete)),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
        ];
    }
}

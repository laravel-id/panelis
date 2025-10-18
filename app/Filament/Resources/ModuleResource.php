<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Enums\ModulePermission;
use App\Filament\Resources\ModuleResource\Pages\ListModules;
use App\Models\Module;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('module.module');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.module');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.system');
    }

    public static function canAccess(): bool
    {
        return user_can(ModulePermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_enabled')
                    ->label(__('module.is_active'))
                    ->disabled(user_cannot(ModulePermission::Edit))
                    ->afterStateUpdated(function () {
                        Cache::forget('modules');
                    }),

                TextColumn::make('name')
                    ->label(__('module.name'))
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('module.description'))
                    ->searchable(),
            ])
            ->filters([
                TernaryFilter::make('is_enabled')
                    ->label(__('module.is_active')),
            ])
            ->recordActions([])
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

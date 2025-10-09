<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Enums\ModulePermission;
use App\Filament\Resources\ModuleResource\Pages;
use App\Models\Module;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('module.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('module.navigation');
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label(__('module.is_active'))
                    ->disabled(user_cannot(ModulePermission::Edit))
                    ->afterStateUpdated(function () {
                        Cache::forget('modules');
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('module.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('module.description'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label(__('module.is_active')),
            ])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListModules::route('/'),
        ];
    }
}

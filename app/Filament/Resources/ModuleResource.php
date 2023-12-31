<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Pages;
use App\Filament\Resources\ModuleResource\RelationManagers;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('module.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('setting.navigation');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-s-rectangle-stack';
    }

    /**
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('Manage module');
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

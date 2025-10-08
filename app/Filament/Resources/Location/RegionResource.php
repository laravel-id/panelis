<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location;
use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use App\Models\Location\Region;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('location.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('location.region.label');
    }

    public static function getLabel(): ?string
    {
        return __('location.region.label');
    }

    public static function canAccess(): bool
    {
        return user_can(RegionPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.location', false) && self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Location\RegionResource\Forms\RegionForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('location.region.is_active'))
                    ->visible(user_can(RegionPermission::Edit)),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('location.region.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('location.country.label'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.region.is_active')),

                SelectFilter::make('country_id')
                    ->label(__('location.country.label'))
                    ->relationship('country', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                EditAction::make()
                    ->visible(user_can(RegionPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(RegionPermission::Delete)),
            ])
            ->bulkActions([
                BulkAction::make('toggle')
                    ->label(__('location.btn.toggle_status'))
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->visible(user_can(RegionPermission::Edit))
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(user_can(RegionPermission::Delete)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Location\RegionResource\Pages\ManageRegions::route('/'),
        ];
    }
}

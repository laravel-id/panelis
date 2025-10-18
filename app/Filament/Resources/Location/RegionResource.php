<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use App\Filament\Resources\Location\RegionResource\Forms\RegionForm;
use App\Filament\Resources\Location\RegionResource\Pages\ManageRegions;
use App\Models\Location\Region;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
        return __('navigation.location');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.region');
    }

    public static function getLabel(): ?string
    {
        return __('location.region');
    }

    public static function canAccess(): bool
    {
        return user_can(RegionPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.location', false) && self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(RegionForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.region_is_active'))
                    ->visible(user_can(RegionPermission::Edit)),

                TextColumn::make('name')
                    ->label(__('location.region_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('country.name')
                    ->label(__('location.country'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->since(get_timezone())
                    ->dateTimeTooltip(get_datetime_format(), get_timezone())
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.region_is_active')),

                SelectFilter::make('country_id')
                    ->label(__('location.country'))
                    ->relationship('country', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(RegionPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(RegionPermission::Delete))
                    ->modalDescription(__('location.delete_confirmation')),
            ])
            ->toolbarActions([
                BulkAction::make('toggle')
                    ->label(__('location.toggle_status'))
                    ->color('primary')
                    ->icon(Heroicon::CheckCircle)
                    ->visible(user_can(RegionPermission::Edit))
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(user_can(RegionPermission::Delete))
                        ->modalDescription(__('location.delete_confirmation')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRegions::route('/'),
        ];
    }
}

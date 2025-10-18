<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\DistrictResource\Enums\DistrictPermission;
use App\Filament\Resources\Location\DistrictResource\Forms\DistrictForm;
use App\Filament\Resources\Location\DistrictResource\Pages\ManageDistricts;
use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use App\Models\Location\District;
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

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.location');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.district');
    }

    public static function getLabel(): ?string
    {
        return __('location.district');
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
            ->components(DistrictForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.district_is_active'))
                    ->visible(user_can(DistrictPermission::Edit)),

                TextColumn::make('name')
                    ->label(__('location.district_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('region.name')
                    ->label(__('location.region'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('region.country.name')
                    ->label(__('location.country'))
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable()
                    ->since(get_timezone())
                    ->dateTimeTooltip(get_datetime_format(), get_timezone())
                    ->since(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location.district_is_active')),

                SelectFilter::make('country_id')
                    ->label(__('location.country'))
                    ->relationship('region.country', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                SelectFilter::make('region_id')
                    ->label(__('location.region'))
                    ->relationship('region', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(DistrictPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(DistrictPermission::Delete)),
            ])
            ->toolbarActions([
                BulkAction::make('toggle')
                    ->label(__('location.toggle_status'))
                    ->color('primary')
                    ->icon(Heroicon::CheckCircle)
                    ->visible(user_can(DistrictPermission::Edit))
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(user_can(DistrictPermission::Delete)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDistricts::route('/'),
        ];
    }
}

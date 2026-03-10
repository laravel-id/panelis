<?php

namespace Modules\Location\Panel\Resources;

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
use Modules\Location\Models\Region;
use Modules\Location\Panel\Resources\RegionResource\Enums\RegionPermission;
use Modules\Location\Panel\Resources\RegionResource\Forms\RegionForm;
use Modules\Location\Panel\Resources\RegionResource\Pages\ManageRegions;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('location::location.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('location::location.region.navigation');
    }

    public static function getLabel(): ?string
    {
        return __('location::location.region.label');
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
            ->components(RegionForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location::location.region.is_active'))
                    ->visible(user_can(RegionPermission::Edit)),

                TextColumn::make('name')
                    ->label(__('location::location.region.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('country.name')
                    ->label(__('location::location.country.label'))
                    ->sortable()
                    ->searchable(),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('location::location.region.is_active')),

                SelectFilter::make('country_id')
                    ->label(__('location::location.country.label'))
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
                    ->visible(user_can(RegionPermission::Delete)),
            ])
            ->toolbarActions([
                BulkAction::make('toggle')
                    ->label(__('location::location.btn.toggle_status'))
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
                        ->visible(user_can(RegionPermission::Delete)),
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

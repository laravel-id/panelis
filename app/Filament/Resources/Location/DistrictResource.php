<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location;
use App\Models\Location\District;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
        return Auth::user()->can('ViewDistrictLocation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.location', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Location\DistrictResource\Forms\DistrictForm::make());
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('UpdateDistrictLocation');
        $canDelete = Auth::user()->can('DeleteDistrictLocation');

        return $table
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('location.district_is_active'))
                    ->visible($canUpdate),

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

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable()
                    ->tooltip(fn (?Model $record): string => $record->local_updated_at->translatedFormat(get_datetime_format()) ?? '')
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
            ->actions([
                EditAction::make()
                    ->visible($canUpdate),

                DeleteAction::make()
                    ->visible($canDelete),
            ])
            ->bulkActions([
                BulkAction::make('toggle')
                    ->label(__('location.toggle_status'))
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->visible($canUpdate)
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = ! $record->is_active;
                            $record->save();
                        }
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible($canDelete),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Location\DistrictResource\Pages\ManageDistricts::route('/'),
        ];
    }
}

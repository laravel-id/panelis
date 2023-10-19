<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Filament\Resources\Location;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Location');
    }

    public static function getNavigationLabel(): string
    {
        return __('District');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-map-pin';
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return __('District');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('region_id')
                    ->translateLabel()
                    ->relationship('region', 'name')
                    ->createOptionForm([
                        Forms\Components\Select::make('country_id')
                            ->translateLabel()
                            ->relationship('country', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(150),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required(),
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->minLength(3)
                    ->maxLength(150),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.country.name')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->translateLabel(),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(__('Country'))
                    ->relationship('region.country', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('region_id')
                    ->label(__('Region'))
                    ->relationship('region', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('toggle')
                    ->label(__('Toggle status'))
                    ->color('primary')
                    ->icon('heroicon-m-check-circle')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->is_active = !$record->is_active;
                            $record->save();
                        }
                    }),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

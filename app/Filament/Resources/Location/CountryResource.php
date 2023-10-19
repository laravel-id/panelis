<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function getNavigationGroup(): ?string
    {
        return __('Location');
    }

    public static function getNavigationLabel(): string
    {
        return __('Country');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-flag';
    }

    public static function getLabel(): ?string
    {
        return __('Country');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('alpha2')
                    ->label(__('Alpha 2'))
                    ->length(2),

                Forms\Components\TextInput::make('alpha3')
                    ->length(3)
                    ->label(__('Alpha 3')),

                Forms\Components\TextInput::make('un_code')
                    ->label(__('UN Code'))
                    ->numeric()
                    ->length(3),

                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('alpha2')
                    ->label(__('Alpha 2')),

                Tables\Columns\TextColumn::make('alpha3')
                    ->label(__('Alpha 3')),

                Tables\Columns\TextColumn::make('un_code')
                    ->translateLabel()
                    ->label(__('UN Code')),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription(__('Are you sure want to do this action? This action will delete related data region and district too.')),
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalDescription(__('Are you sure want to do this action? This action will delete related data region and district too.')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CountryResource\Pages\ManageCountries::route('/'),
        ];
    }
}

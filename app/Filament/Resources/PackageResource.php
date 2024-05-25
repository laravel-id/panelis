<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use NumberFormatter;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),

                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('price')
                    ->label(__('package.unit_price'))
                    ->required()
                    ->numeric()
                    ->maxLength(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        numfmt_format_currency(numfmt_create('id_ID', NumberFormatter::CURRENCY), 1000, 'IDR');
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('unit.name'),
                TextColumn::make('price')
                    ->label(__('package.unit_price'))
                    ->alignEnd()
                    ->weight(FontWeight::SemiBold)
                    ->numeric(locale: 'id')
                    ->prefix('Rp'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}

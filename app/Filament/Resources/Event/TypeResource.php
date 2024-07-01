<?php

namespace App\Filament\Resources\Event;

use App\Filament\Resources\Event\TypeResource\Pages;
use App\Models\Event\Type;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.event');
    }

    public static function getLabel(): ?string
    {
        return __('event.type');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('event.type_title'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (! empty($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->required(),

                        TextInput::make('slug')
                            ->label(__('event.type_slug'))
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('event.type_title')),

                TextColumn::make('created_at')
                    ->label(__('ui.created_at'))
                    ->date()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([

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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}

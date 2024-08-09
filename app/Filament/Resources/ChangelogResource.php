<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChangelogResource\Forms\ChangelogForm;
use App\Filament\Resources\ChangelogResource\Pages;
use App\Filament\Resources\ChangelogResource\RelationManagers;
use App\Models\Changelog;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('changelog.changelog');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.changelog');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('changelog.title'))
                    ->schema(ChangelogForm::make()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logged_at')
                    ->dateTime(get_datetime_format()),

                TextColumn::make('title')
                    ->label(__('changelog.title'))
                    ->url(fn(Changelog $changelog): ?string => $changelog->url)
                    ->description(fn(Changelog $changelog): ?string => $changelog->description),

                TextColumn::make('local_created_at')
                    ->label(__('ui.created_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListChangelogs::route('/'),
            'create' => Pages\CreateChangelog::route('/create'),
            'edit' => Pages\EditChangelog::route('/{record}/edit'),
        ];
    }
}

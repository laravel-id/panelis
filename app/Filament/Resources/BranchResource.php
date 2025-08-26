<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\BranchForm;
use App\Filament\Resources\BranchResource\Enums\BranchPermission;
use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('branch.label');
    }

    public static function canAccess(): bool
    {
        return user_can(BranchPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(BranchForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label(__('branch.logo'))
                    ->height(25)
                    ->disk('public'),

                TextColumn::make('name')
                    ->label(__('branch.name'))
                    ->weight(FontWeight::Bold)
                    ->url(fn (Branch $record): string => Pages\ViewBranch::getUrl(['record' => $record->id]))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label(__('branch.address'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('branch.phone'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->since()
                    ->dateTimeTooltip(get_datetime_format(), get_timezone()),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->visible(user_can(BranchPermission::Edit)),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->disabled(fn (Branch $record): bool => $record->is(Filament::getTenant()))
                    ->visible(user_can(BranchPermission::Delete)),
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
            'index' => Pages\ListBranches::route('/'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
            'view' => Pages\ViewBranch::route('/{record}'),
        ];
    }
}

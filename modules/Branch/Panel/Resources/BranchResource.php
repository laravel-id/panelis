<?php

namespace Modules\Branch\Panel\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Branch\Models\Branch;
use Modules\Branch\Panel\Resources\BranchResource\Enums\BranchPermission;
use Modules\Branch\Panel\Resources\BranchResource\Forms\BranchForm;
use Modules\Branch\Panel\Resources\BranchResource\Pages;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::BuildingOffice;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('branch::branch.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('branch::branch.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(BranchPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess() && (bool) config('panelis.multitenant');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(BranchForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label(__('branch::branch.logo'))
                    ->height(25)
                    ->disk('public'),

                TextColumn::make('name')
                    ->label(__('branch::branch.name'))
                    ->weight(FontWeight::Bold)
                    ->url(fn (Branch $record): string => Pages\ViewBranch::getUrl(['record' => $record->id]))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label(__('branch::branch.address'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('branch::branch.phone'))
                    ->sortable()
                    ->searchable(),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(BranchPermission::Edit)),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->disabled(fn (Branch $record): bool => $record->is(Filament::getTenant()))
                    ->visible(user_can(BranchPermission::Delete)),
            ])
            ->toolbarActions([]);
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

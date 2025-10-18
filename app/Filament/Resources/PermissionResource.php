<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages\ManagePermissions;
use App\Models\Permission;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('user.permission.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.permission.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('user.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(PermissionResource\Enums\Permission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label(__('user.permission.name'))
                    ->disabledOn('edit')
                    ->required()
                    ->unique(ignorable: $schema->getRecord())
                    ->minLength(3)
                    ->maxLength(30),

                TextInput::make('guard_name')
                    ->label(__('user.permission.guard_name'))
                    ->disabledOn('edit')
                    ->default('web')
                    ->datalist(['web', 'api'])
                    ->required(),

                TextEntry::make('label')
                    ->label(__('user.permission.name'))
                    ->visibleOn('edit')
                    ->state(fn (Permission $permission): string => $permission->label),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('user.permission.name'))
                    ->searchable(['name', 'label', 'description'])
                    ->sortable()
                    ->description(fn (?Model $record): string => $record?->description ?? ''),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(PermissionResource\Enums\Permission::Edit)),

                ActionGroup::make([
                    DeleteAction::make()
                        ->visible(user_can(PermissionResource\Enums\Permission::Delete)),
                ]),
            ])
            ->toolbarActions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePermissions::route('/'),
        ];
    }
}

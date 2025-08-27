<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
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
        return __('user.permission');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.permission');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.user');
    }

    public static function canAccess(): bool
    {
        return user_can(PermissionResource\Enums\Permission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                TextInput::make('name')
                    ->label(__('user.permission_name'))
                    ->disabledOn('edit')
                    ->required()
                    ->unique(ignorable: $form->getRecord())
                    ->minLength(3)
                    ->maxLength(30),

                TextInput::make('guard_name')
                    ->label(__('user.permission_guard_name'))
                    ->disabledOn('edit')
                    ->default('web')
                    ->datalist(['web', 'api'])
                    ->required(),

                Placeholder::make('label')
                    ->label(__('user.permission_name'))
                    ->visibleOn('edit')
                    ->content(fn (Permission $permission): string => $permission->label),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('user.permission_name'))
                    ->searchable(['name', 'label', 'description'])
                    ->sortable()
                    ->description(fn (?Model $record): string => $record?->description ?? ''),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->visible(user_can(PermissionResource\Enums\Permission::Edit)),

                ActionGroup::make([
                    DeleteAction::make()
                        ->visible(user_can(PermissionResource\Enums\Permission::Delete)),
                ]),
            ])
            ->bulkActions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}

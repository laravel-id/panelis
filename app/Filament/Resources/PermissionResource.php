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
use Illuminate\Support\Facades\Auth;

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

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View permission');
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
                    ->maxLength(20),

                TextInput::make('guard_name')
                    ->label('user.permission_guard_name')
                    ->disabledOn('edit')
                    ->default('web')
                    ->required(),

                Placeholder::make('label')
                    ->label(__('user.permission_label'))
                    ->visibleOn('edit')
                    ->content(fn (Permission $permission): string => $permission->label),

                Placeholder::make('description')
                    ->label('user.permission_description')
                    ->visibleOn('edit')
                    ->content(fn (Permission $permission): string => $permission->description),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update permission');
        $canDelete = Auth::user()->can('Delete permission');

        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('user.permission_label'))
                    ->searchable(['name', 'label', 'description'])
                    ->sortable()
                    ->description(fn (?Model $record): string => $record?->description ?? ''),

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->visible($canUpdate),

                ActionGroup::make([
                    DeleteAction::make()
                        ->visible($canDelete)
                        ->disabled(fn (Permission $record): bool => $record->is_default),
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

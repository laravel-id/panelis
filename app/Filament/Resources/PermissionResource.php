<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                Forms\Components\TextInput::make('name')
                    ->label(__('user.permission_name'))
                    ->disabledOn('edit')
                    ->required()
                    ->unique(ignorable: $form->getRecord())
                    ->minLength(3)
                    ->maxLength(20),

                Forms\Components\Textarea::make('description')
                    ->label(__('user.permission_description'))
                    ->maxLength(250),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update permission');
        $canDelete = Auth::user()->can('Delete permission');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.permission_name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (?Model $record): string => $record?->description ?? ''),

                Tables\Columns\TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible($canUpdate),

                Tables\Actions\DeleteAction::make()
                    ->visible($canDelete)
                    ->disabled(fn (?Model $record): bool => $record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible($canDelete),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}

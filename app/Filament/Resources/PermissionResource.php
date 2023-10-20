<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-open';

    protected static ?int $navigationSort = 1;

    public static function getLabel(): ?string
    {
        return __('Permission');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User management');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-lock-open';
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
                    ->translateLabel()
                    ->disabledOn('edit')
                    ->required()
                    ->unique(ignorable: $form->getRecord())
                    ->minLength(3)
                    ->maxLength(20),

                Forms\Components\Textarea::make('description')
                    ->translateLabel()
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
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->description(fn(?Model $record): string => $record?->description ?? ''),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible($canUpdate),

                Tables\Actions\DeleteAction::make()
                    ->visible($canDelete)
                    ->disabled(fn(?Model $record): bool => $record->is_default),
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

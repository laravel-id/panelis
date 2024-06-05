<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('User management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Role');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-s-user-group';
    }

    public static function getLabel(): ?string
    {
        return __('Role');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View role');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make('Role')
                    ->description('Create a new role that to be assigned to user')
                    ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignorable: $form->getRecord())
                            ->minLength(3)
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->maxLength(250),
                    ]),

                Forms\Components\Section::make()
                    ->hiddenOn(Pages\CreateRole::class)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (?Model $record): string => $record->created_at),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (?Model $record): string => $record->updated_at),
                    ]),

                Forms\Components\Section::make('Permission')
                    ->label(__('Permission'))
                    ->description(__('Set default permission for role'))
                    ->schema([
                        Forms\Components\CheckboxList::make('permission_id')
                            ->label(__('Permission'))
                            ->columns(3)
                            ->gridDirection('row')
                            ->searchable()
                            ->bulkToggleable()
                            ->relationship('permissions', 'name')
                            ->descriptions(
                                Permission::pluck('description', 'id'),
                            )
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update role');
        $canDelete = Auth::user()->can('Delete role');

        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->description(fn (?Model $record): string => $record->description),

                Tables\Columns\TextColumn::make('users_count')
                    ->translateLabel()
                    ->counts('users')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->tooltip(fn (?Model $record): string => $record->updated_at)
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible($canUpdate),
                Tables\Actions\DeleteAction::make()
                    ->disabled(function (?Model $record): bool {
                        return $record->users_count >= 1;
                    })
                    ->visible($canDelete),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

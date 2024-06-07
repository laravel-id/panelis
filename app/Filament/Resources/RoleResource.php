<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.role');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-s-user-group';
    }

    public static function getLabel(): ?string
    {
        return __('user.role');
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
                Forms\Components\Section::make(__('user.role'))
                    ->description(__('user.role_section_description'))
                    ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('user.role_name'))
                            ->required()
                            ->unique(ignorable: $form->getRecord())
                            ->minLength(3)
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->label(__('user.role_description'))
                            ->required()
                            ->rows(3)
                            ->maxLength(250),
                    ]),

                Forms\Components\Section::make()
                    ->hiddenOn(Pages\CreateRole::class)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('ui.created_at'))
                            ->content(fn (Role $role): string => $role->local_created_at),

                        Forms\Components\Placeholder::make('local_updated_at')
                            ->label(__('ui.updated_at'))
                            ->content(fn (Role $role): string => $role->local_updated_at),
                    ]),

                Forms\Components\Section::make(__('user.permission'))
                    ->description(__('user.permission_section_description'))
                    ->schema([
                        Forms\Components\CheckboxList::make('permission_id')
                            ->label(__('user.permission'))
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
                    ->label(__('user.role_name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Role $role): string => $role->description),

                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('user.role_user_count'))
                    ->counts('users')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('local_updated_at')
                    ->label(__('user.role_updated_at'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible($canUpdate),
                Tables\Actions\DeleteAction::make()
                    ->disabled(function (Role $role): bool {
                        return $role->users_count >= 1;
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

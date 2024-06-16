<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 3;

    protected static ?string $tenantOwnershipRelationshipName = 'branches';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.user');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-s-user';
    }

    public static function getLabel(): ?string
    {
        return __('user.user');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make()
                    ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                    ->translateLabel()
                    ->schema([
                        TextInput::make('email')
                            ->label(__('user.email'))
                            ->required()
                            ->unique(ignorable: $form->getRecord())
                            ->email(),

                        TextInput::make('name')
                            ->label('user.name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(150),
                    ]),

                Section::make()
                    ->hiddenOn(Pages\CreateUser::class)
                    ->columnSpan(1)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label(__('ui.created_at'))
                            ->visibleOn([
                                Pages\ViewUser::class,
                                Pages\EditUser::class,
                            ])
                            ->content(fn (User $user): string => $user->local_created_at),

                        Placeholder::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->visibleOn([
                                Pages\ViewUser::class,
                                Pages\EditUser::class,
                            ])
                            ->content(fn (User $user): string => $user->local_updated_at),
                    ]),

                Section::make(__('user.branch'))
                    ->description(__('user.branch_section_description'))
                    ->schema([
                        CheckboxList::make('branches')
                            ->label(__('user.branch'))
                            ->relationship('branches')
                            ->bulkToggleable()
                            ->options(
                                Branch::orderBy('name')
                                    ->pluck('name', 'id'),
                            )
                            ->required(),
                    ]),

                Section::make(__('user.role'))
                    ->description(__('user.role_section_description'))
                    ->schema([
                        CheckboxList::make('role_id')
                            ->label(__('user.role_name'))
                            ->relationship('roles', 'name')
                            ->descriptions(Role::pluck('description', 'id'))
                            ->required(fn (User $user): bool => ! $user->isRoot()),
                    ]),

                Section::make(__('user.profile'))
                    ->description(__('user.profile_section_description'))
                    ->collapsed()
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('user.phone'))
                            ->tel()
                            ->maxLength(20),

                        Textarea::make('address')
                            ->label(__('user.address'))
                            ->rows(4)
                            ->minLength(3)
                            ->maxLength(150),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branches.name')
                    ->label(__('user.branch')),

                TextColumn::make('roles.name')
                    ->label(__('user.role'))
                    ->default('*'),

                TextColumn::make('name')
                    ->label(__('user.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('user.name'))
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('local_created_at')
                    ->label(__('ui.created_at'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch')
                    ->label(__('user.branch'))
                    ->preload()
                    ->multiple()
                    ->relationship('branches', 'name'),

                SelectFilter::make('role')
                    ->label(__('user.role'))
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                EditAction::make()->visible(Auth::user()->can('Update user')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

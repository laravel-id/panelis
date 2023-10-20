<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserStatsOverview;
use App\Models\District;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('User management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-m-users';
    }

    public static function getLabel(): ?string
    {
        return __('User');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Basic info'))
                    ->translateLabel()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->HiddenOn(Pages\EditUser::class)
                            ->unique(ignorable: $form->getRecord())
                            ->email(),

                        Forms\Components\Placeholder::make('email')
                            ->content(fn(?Model $record): string => $record->email)
                            ->visibleOn(Pages\EditUser::class),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(150),

                        Forms\Components\Placeholder::make('created_at')
                            ->visibleOn(Pages\ViewUser::class)
                            ->content(fn(?Model $record): string => $record->created_at),

                        Forms\Components\Placeholder::make('updated_at')
                            ->visibleOn(Pages\ViewUser::class)
                            ->content(fn(?Model $record): string => $record->updated_at),
                    ]),

                Forms\Components\Section::make(__('Role'))
                    ->description(__('If no role selected, user will be assigned as super user'))
                    ->schema([
                        Forms\Components\CheckboxList::make('role_id')
                            ->label(__('Role'))
                            ->relationship('roles', 'name')
                            ->descriptions(Role::pluck('description', 'id')),
                    ]),

                Forms\Components\Section::make(__('Additional info'))
                    ->collapsed()
                    ->translateLabel()
                    ->relationship('profile')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('address')
                            ->rows(4)
                            ->minLength(3)
                            ->maxLength(150),

                        Forms\Components\Select::make('district_id')
                            ->placeholder(__('Select location'))
                            ->label(__('District'))
                            ->options(District::pluck('name', 'id'))
                            ->searchable()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel()
                    ->default('*'),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->translateLabel()
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->tooltip(fn(?Model $record): string => $record->created_at)
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Role')
                    ->translateLabel()
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(Auth::user()->can('View user')),
                Tables\Actions\EditAction::make()->visible(Auth::user()->can('Update user')),
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

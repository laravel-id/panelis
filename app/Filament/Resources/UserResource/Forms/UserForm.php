<?php

namespace App\Filament\Resources\UserResource\Forms;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function schema(?string $operation): array
    {
        return [
            Section::make()
                ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                ->translateLabel()
                ->schema([
                    FileUpload::make('avatar')
                        ->hiddenLabel()
                        ->disk('public')
                        ->directory('avatars')
                        ->avatar()
                        ->alignCenter()
                        ->moveFiles()
                        ->image(),

                    Grid::make()
                        ->schema([
                            TextInput::make('name')
                                ->label(__('user.name'))
                                ->required()
                                ->minLength(3)
                                ->maxLength(150),

                            TextInput::make('email')
                                ->label(__('user.email'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->email()
                                ->dehydrateStateUsing(fn (string $state): string => strtolower($state)),

                        ]),

                    Toggle::make('send_reset_password_link')
                        ->label(__('user.let_user_reset_password'))
                        ->live()
                        ->visibleOn(CreateUser::class)
                        ->default(true),

                    Fieldset::make(__('user.password'))
                        ->visible(function (Get $get) use ($operation): bool {
                            return $operation === 'create'
                                && ! $get('send_reset_password_link');
                        })
                        ->schema([
                            TextInput::make('password')
                                ->label(__('user.password'))
                                ->password()
                                ->confirmed()
                                ->autocomplete(false)
                                ->revealable(),

                            TextInput::make('password_confirmation')
                                ->label(__('user.password_confirmation'))
                                ->password()
                                ->autocomplete(false)
                                ->revealable(),
                        ]),
                ]),

            Section::make()
                ->hiddenOn(CreateUser::class)
                ->columnSpan(1)
                ->schema([
                    Placeholder::make('created_at')
                        ->label(__('ui.created_at'))
                        ->visibleOn([
                            ViewUser::class,
                            EditUser::class,
                        ])
                        ->content(fn (User $user): string => $user->created_at->timezone(get_timezone())),

                    Placeholder::make('updated_at')
                        ->label(__('ui.updated_at'))
                        ->visibleOn([
                            ViewUser::class,
                            EditUser::class,
                        ])
                        ->content(fn (User $user): string => $user->updated_at->timezone(get_timezone())),
                ]),

            Section::make(__('branch.label'))
                ->description(__('user.branch_section_description'))
                ->visible(fn (): bool => ! empty(Filament::getTenant()))
                ->schema([
                    CheckboxList::make('branches')
                        ->label(__('branch.label'))
                        ->relationship('branches')
                        ->bulkToggleable()
                        ->options(
                            Branch::orderBy('name')
                                ->pluck('name', 'id'),
                        )
                        ->required(),
                ]),

            Section::make(__('user.role.label'))
                ->schema([
                    CheckboxList::make('role_id')
                        ->label(__('user.role.name'))
                        ->relationship('roles', 'name')
                        ->getOptionLabelFromRecordUsing(function (Role $role): string {
                            $label = $role->name;
                            if ($role->is_admin) {
                                $label .= sprintf(' (%s)', __('user.role.admin_access'));
                            }

                            return $label;
                        })
                        ->required(fn (User $user): bool => ! $user->is_root),
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
        ];
    }
}

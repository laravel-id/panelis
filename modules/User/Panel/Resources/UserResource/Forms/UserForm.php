<?php

namespace Modules\User\Panel\Resources\UserResource\Forms;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Branch\Models\Branch;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\User\Panel\Resources\UserResource\Pages\CreateUser;

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
                                ->label(__('user::user.name'))
                                ->required()
                                ->minLength(3)
                                ->maxLength(150),

                            TextInput::make('email')
                                ->label(__('user::user.email'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->email()
                                ->dehydrateStateUsing(fn (string $state): string => strtolower($state)),

                        ]),

                    Toggle::make('send_reset_password_link')
                        ->label(__('user::user.let_user_reset_password'))
                        ->live()
                        ->visibleOn(CreateUser::class)
                        ->default(true),

                    Fieldset::make(__('user::user.password'))
                        ->visible(function (Get $get) use ($operation): bool {
                            return $operation === 'create'
                                && ! $get('send_reset_password_link');
                        })
                        ->schema([
                            TextInput::make('password')
                                ->label(__('user::user.password'))
                                ->password()
                                ->confirmed()
                                ->autocomplete(false)
                                ->copyable()
                                ->revealable(),

                            TextInput::make('password_confirmation')
                                ->label(__('user::user.password_confirmation'))
                                ->password()
                                ->autocomplete(false)
                                ->revealable(),

                            Actions::make([
                                Action::make('generate_password')
                                    ->label(__('user::user.btn.generate_password'))
                                    ->action(function (Set $set): void {
                                        $password = Str::password(8);

                                        $set('password', $password);
                                        $set('password_confirmation', $password);
                                    }),
                            ]),
                        ]),
                ]),

            Section::make()
                ->hiddenOn(CreateUser::class)
                ->columnSpan(1)
                ->schema([
                    TextEntry::makeSinceDate('created_at', __('ui.created_at')),

                    TextEntry::makeSinceDate('updated_at', __('ui.updated_at')),
                ]),

            Section::make(__('branch.label'))
                ->description(__('user::user.branch_section_description'))
                ->visible(fn (): bool => ! empty(Filament::getTenant()))
                ->columnSpanFull()
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

            Section::make(__('user::user.role.label'))
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make('role_id')
                        ->label(__('user::user.role.name'))
                        ->relationship('roles', 'name')
                        ->disabled(function (?User $record): bool {
                            if (empty($record)) {
                                return false;
                            }

                            return $record->getKey() === Auth::id() && Auth::user()->is_root;
                        })
                        ->getOptionLabelFromRecordUsing(function (Role $role): string {
                            $label = $role->name;
                            if ($role->is_admin) {
                                $label .= sprintf(' (%s)', __('user::user.role.admin_access'));
                            }

                            return $label;
                        })
                        ->required(function (?User $record): bool {
                            return empty($record) || ! ($record?->is_root ?? false);
                        }),
                ]),

            Section::make(__('user::user.profile'))
                ->description(__('user::user.profile_section_description'))
                ->collapsed()
                ->relationship('profile')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('phone')
                        ->label(__('user::user.phone'))
                        ->tel()
                        ->maxLength(20),

                    Textarea::make('address')
                        ->label(__('user::user.address'))
                        ->rows(4)
                        ->minLength(3)
                        ->maxLength(150),
                ]),
        ];
    }
}

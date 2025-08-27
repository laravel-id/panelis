<?php

namespace App\Filament\Resources;

use App\Actions\User\SendResetPasswordLink;
use App\Filament\Resources\UserResource\Enums\UserPermission;
use App\Filament\Resources\UserResource\Forms\UserForm;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $tenantOwnershipRelationshipName = 'branches';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.user');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.user');
    }

    public static function getLabel(): ?string
    {
        return __('user.user');
    }

    public static function canAccess(): bool
    {
        return user_can(UserPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        $operation = $form->getOperation();

        return $form
            ->columns(3)
            ->schema(UserForm::schema($operation));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branches.name')
                    ->label(__('user.branch'))
                    ->visible(fn (): bool => ! empty(Filament::getTenant())),

                TextColumn::make('roles.name')
                    ->label(__('user.role'))
                    ->default('*'),

                ImageColumn::make('avatar')
                    ->defaultImageUrl(function (User $record): string {
                        $customAvatar = $record->getFilamentAvatarUrl();
                        if (empty($customAvatar)) {
                            return 'https://ui-avatars.com/api/?name='.urlencode($record->name);
                        }

                        return $customAvatar;
                    }),

                TextColumn::make('name')
                    ->label(__('user.name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->label(__('user.email'))
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
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
                EditAction::make()->visible(user_can(UserPermission::Edit)),

                ActionGroup::make([
                    Action::make('send_reset_password_link')
                        ->label(__('user.btn_send_reset_password_link'))
                        ->icon(__('heroicon-o-lock-open'))
                        ->visible(user_can(UserPermission::ResetPassword))
                        ->disabled(fn (User $user): bool => Auth::id() === $user->id)
                        ->requiresConfirmation()
                        ->action(function (User $user): void {
                            try {
                                SendResetPasswordLink::run($user);

                                Notification::make('link_sent')
                                    ->title(__('user.reset_password_link_sent'))
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Log::error($e);

                                Notification::make('link_not_sent')
                                    ->title(__('user.reset_password_link_not_sent'))
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
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

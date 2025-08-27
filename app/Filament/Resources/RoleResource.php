<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Enums\RolePermission;
use App\Filament\Resources\RoleResource\Forms\RoleForm;
use App\Filament\Resources\RoleResource\Pages;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.user');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.role');
    }

    public static function getLabel(): ?string
    {
        return __('user.role');
    }

    public static function canAccess(): bool
    {
        return user_can(RolePermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        $permissionForms = Permission::query()
            ->get()
            ->groupBy(function (Permission $permission): string {
                $labels = explode('_', $permission->getRawOriginal('label'));

                return end($labels);
            })
            ->sortKeys()
            ->map(function (Collection $permissions, string $groupLabel) {
                return Section::make(__('user.permission_'.$groupLabel))
                    ->collapsible()
                    ->schema([
                        CheckboxList::make("permissions_{$groupLabel}")
                            ->columns(3)
                            ->hiddenLabel()
                            ->options($permissions->pluck('label', 'id')->toArray())
                            ->bulkToggleable()
                            ->searchable()
                            ->afterStateHydrated(function (CheckboxList $component, ?Role $record) use ($permissions): void {
                                if (empty($record)) {
                                    return;
                                }

                                $selectedIds = $record->permissions()
                                    ->whereIn('id', $permissions->pluck('id'))
                                    ->pluck('permissions.id')
                                    ->toArray();

                                $component->state($selectedIds);
                            })
                            ->saveRelationshipsUsing(function (Component $component, array $state, ?Role $record) use ($permissions): void {
                                if (empty($record)) {
                                    return;
                                }

                                $currentPermissions = $record->permissions()
                                    ->whereIn('id', $permissions->pluck('id'))
                                    ->pluck('id')
                                    ->toArray();

                                $toDetach = array_diff($currentPermissions, $state ?? []);
                                $toAttach = array_diff($state ?? [], $currentPermissions);

                                if (! empty($toDetach)) {
                                    $record->permissions()->detach($toDetach);
                                }
                                if (! empty($toAttach)) {
                                    $record->permissions()->attach($toAttach);
                                }
                            })
                            ->dehydrated(false),
                    ]);
            })
            ->values()
            ->toArray();

        return $form
            ->columns(3)
            ->schema([
                Section::make(__('user.role'))
                    ->description(__('user.role_section_description'))
                    ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                    ->schema(RoleForm::schema()),

                Section::make()
                    ->hiddenOn(Pages\CreateRole::class)
                    ->columnSpan(1)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label(__('ui.created_at'))
                            ->content(fn (Role $record): string => $record->created_at->timezone(get_timezone())->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label(__('ui.updated_at'))
                            ->content(fn (Role $record): string => $record->updated_at->timezone(get_timezone())->diffForHumans()),
                    ]),

                Section::make('Permission')
                    ->schema($permissionForms),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                ToggleColumn::make('is_admin')
                    ->label(__('user.role_is_admin'))
                    ->disabled(! user_can(RolePermission::Edit)),

                TextColumn::make('name')
                    ->label(__('user.role_name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('users_count')
                    ->label(__('user.role_user_count'))
                    ->counts('users')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->since(get_timezone())
                    ->dateTimeTooltip(get_datetime_format(), get_timezone())
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->visible(user_can(RolePermission::Edit)),

                ActionGroup::make([
                    DeleteAction::make()
                        ->disabled(function (Role $role): bool {
                            return $role->users_count >= 1;
                        })
                        ->visible(user_can(RolePermission::Delete)),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

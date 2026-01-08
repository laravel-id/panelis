<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Enums\RolePermission;
use App\Filament\Resources\RoleResource\Forms\RoleForm;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Resources\RoleResource\Pages\EditRole;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Resources\RoleResource\Pages\ViewRole;
use App\Models\Permission;
use App\Models\Role;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Filament\Support\Enums\FontWeight;
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
        return __('user.navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.role.navigation');
    }

    public static function getLabel(): ?string
    {
        return __('user.role.label');
    }

    public static function canAccess(): bool
    {
        return user_can(RolePermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        $permissionForms = Permission::query()
            ->get()
            ->groupBy(function (Permission $permission): string {
                $labels = explode('_', $permission->getRawOriginal('label'));

                return end($labels);
            })
            ->sortKeys()
            ->map(function (Collection $permissions, string $groupLabel) {
                return Section::make(__('user.permission.'.$groupLabel))
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

        return $schema
            ->columns(3)
            ->components([
                Section::make(__('user.role'))
                    ->description(__('user.role.section_description'))
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('user.role.label'))
                            ->description(__('user.role.section_description'))
                            ->columnSpan(fn (?Model $record): int => empty($record) ? 3 : 2)
                            ->schema(RoleForm::schema()),

                        Section::make()
                            ->hiddenOn(CreateRole::class)
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('ui.created_at'))
                                    ->dateTimeTooltip(get_datetime_format())
                                    ->since(),

                                TextEntry::make('updated_at')
                                    ->label(__('ui.updated_at'))
                                    ->dateTimeTooltip(get_datetime_format())
                                    ->since(),
                            ]),

                        Section::make('Permission')
                            ->schema($permissionForms),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                ToggleColumn::make('is_admin')
                    ->label(__('user.role.is_admin'))
                    ->disabled(! user_can(RolePermission::Edit)),

                TextColumn::make('name')
                    ->label(__('user.role.name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('users_count')
                    ->label(__('user.role.user_count'))
                    ->counts('users')
                    ->color('primary')
                    ->sortable(),

                TextColumn::makeSinceDate('updated_at', __('ui.updated_at')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->visible(user_can(RolePermission::Edit)),

                ActionGroup::make([
                    DeleteAction::make()
                        ->disabled(function (Role $role): bool {
                            return $role->users_count >= 1;
                        })
                        ->visible(user_can(RolePermission::Delete)),
                ]),
            ])
            ->toolbarActions([

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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}

<?php

namespace Modules\User\Panel\Resources\RoleResource\Forms;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

class RoleForm
{
    public static function schema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->label(__('user::user.role.name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength(3)
                        ->maxLength(50),

                    TextInput::make('guard_name')
                        ->label(__('user::user.role.guard_name'))
                        ->default('web')
                        ->datalist(['web', 'api'])
                        ->required()
                        ->alphaDash(),
                ]),

            Toggle::make('is_admin')
                ->label(__('user::user.role.is_admin'))
                ->live(),
        ];
    }
}

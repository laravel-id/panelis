<?php

namespace App\Filament\Resources\RoleResource\Forms;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RoleForm
{
    public static function schema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('user.role_name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength(3)
                        ->maxLength(50),

                    TextInput::make('guard_name')
                        ->label(__('user.role_guard_name'))
                        ->default('web')
                        ->required()
                        ->alphaDash(),
                ]),

            Textarea::make('description')
                ->label(__('user.role_description'))
                ->rows(3)
                ->maxLength(250),

            Toggle::make('is_admin')
                ->label(__('user.role_is_admin'))
                ->live(),
        ];
    }
}

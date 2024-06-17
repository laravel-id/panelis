<?php

namespace App\Filament\Resources\RoleResource\Forms;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class RoleForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('user.role_name'))
                ->required()
                ->unique(ignoreRecord: true)
                ->minLength(3)
                ->maxLength(50),

            Textarea::make('description')
                ->label(__('user.role_description'))
                ->required()
                ->rows(3)
                ->maxLength(250),
        ];
    }
}

<?php

namespace App\Filament\Resources\Location\CountryResource\Forms;

use Filament\Forms\Components\TextInput;

class CountryForm
{
    public static function make(): array
    {
        return [
            TextInput::make('alpha2')
                ->label(__('location.alpha2'))
                ->length(2),

            TextInput::make('alpha3')
                ->length(3)
                ->label(__('location.alpha3')),

            TextInput::make('un_code')
                ->label(__('location.un_code'))
                ->numeric()
                ->length(3),

            TextInput::make('name')
                ->label(__('location.name'))
                ->required()
                ->maxLength(100)
                ->columnSpanFull(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Location\CountryResource\Forms;

use Filament\Forms\Components\TextInput;

class CountryForm
{
    public static function make(): array
    {
        return [
            TextInput::make('alpha2')
                ->label(__('location.country.alpha2'))
                ->length(2),

            TextInput::make('alpha3')
                ->length(3)
                ->label(__('location.country.alpha3')),

            TextInput::make('un_code')
                ->label(__('location.country.un_code'))
                ->numeric()
                ->length(3),

            TextInput::make('name')
                ->label(__('location.country.name'))
                ->required()
                ->maxLength(100)
                ->columnSpanFull(),
        ];
    }
}

<?php

namespace Modules\Location\Panel\Resources\CountryResource\Forms;

use Filament\Forms\Components\TextInput;

class CountryForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('alpha2')
                ->label(__('location::location.country.alpha2'))
                ->length(2),

            TextInput::make('alpha3')
                ->length(3)
                ->label(__('location::location.country.alpha3')),

            TextInput::make('un_code')
                ->label(__('location::location.country.un_code'))
                ->numeric()
                ->length(3),

            TextInput::make('name')
                ->label(__('location::location.country.name'))
                ->required()
                ->maxLength(100)
                ->columnSpanFull(),
        ];
    }
}

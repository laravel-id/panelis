<?php

namespace App\Filament\Resources\Location\RegionResource\Forms;

use App\Filament\Resources\Location\CountryResource\Enums\CountryPermission;
use App\Filament\Resources\Location\CountryResource\Forms\CountryForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RegionForm
{
    public static function schema(): array
    {
        return [
            Select::make('country_id')
                ->label(__('location.country.label'))
                ->relationship('country', 'name')
                ->createOptionForm(user_can(CountryPermission::Add) ? CountryForm::schema() : null)
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('name')
                ->label(__('location.region.name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

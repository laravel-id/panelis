<?php

namespace App\Filament\Resources\Location\RegionResource\Forms;

use App\Filament\Resources\Location\CountryResource\Enums\CountryPermission;
use App\Filament\Resources\Location\CountryResource\Forms\CountryForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RegionForm
{
    public static function make(): array
    {
        return [
            Select::make('country_id')
                ->label(__('location.country'))
                ->relationship('country', 'name')
                ->createOptionForm(user_can(CountryPermission::Add) ? CountryForm::make() : null)
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('name')
                ->label(__('location.region_name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

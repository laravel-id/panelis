<?php

namespace Modules\Location\Panel\Resources\RegionResource\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Location\Panel\Resources\CountryResource\Enums\CountryPermission;
use Modules\Location\Panel\Resources\CountryResource\Forms\CountryForm as FormsCountryForm;

class RegionForm
{
    public static function schema(): array
    {
        return [
            Select::make('country_id')
                ->label(__('location::location.country.label'))
                ->relationship('country', 'name')
                ->createOptionForm(user_can(CountryPermission::Add) ? FormsCountryForm::schema() : null)
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('name')
                ->label(__('location::location.region.name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

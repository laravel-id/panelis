<?php

namespace App\Filament\Resources\Location\DistrictResource\Forms;

use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use App\Filament\Resources\Location\RegionResource\Forms\RegionForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DistrictForm
{
    public static function make(): array
    {
        return [
            Select::make('region_id')
                ->label(__('location.region'))
                ->relationship('region', 'name')
                ->createOptionForm(user_can(RegionPermission::Add) ? RegionForm::make() : null)
                ->preload()
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label(__('location.district_name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

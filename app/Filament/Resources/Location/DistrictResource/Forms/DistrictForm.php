<?php

namespace App\Filament\Resources\Location\DistrictResource\Forms;

use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use App\Filament\Resources\Location\RegionResource\Forms\RegionForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DistrictForm
{
    public static function schema(): array
    {
        return [
            Select::make('region_id')
                ->label(__('location.region.label'))
                ->relationship('region', 'name')
                ->createOptionForm(user_can(RegionPermission::Add) ? RegionForm::schema() : null)
                ->preload()
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label(__('location.district.name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

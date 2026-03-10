<?php

namespace Modules\Location\Panel\Resources\DistrictResource\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Location\Panel\Resources\RegionResource\Enums\RegionPermission;
use Modules\Location\Panel\Resources\RegionResource\Forms\RegionForm;

class DistrictForm
{
    public static function schema(): array
    {
        return [
            Select::make('region_id')
                ->label(__('location::location.region.label'))
                ->relationship('region', 'name')
                ->createOptionForm(user_can(RegionPermission::Add) ? RegionForm::schema() : null)
                ->preload()
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label(__('location::location.district.name'))
                ->required()
                ->minLength(3)
                ->maxLength(150),
        ];
    }
}

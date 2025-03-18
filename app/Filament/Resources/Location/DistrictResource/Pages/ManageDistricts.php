<?php

namespace App\Filament\Resources\Location\DistrictResource\Pages;

use App\Filament\Resources\Location\DistrictResource;
use App\Filament\Resources\Location\DistrictResource\Enums\DistrictPermission;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;

class ManageDistricts extends ManageRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(user_can(DistrictPermission::Add)),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);
        abort_unless(
            user_can(DistrictPermission::Browse) && user_can(DistrictPermission::Add),
            Response::HTTP_FORBIDDEN,
        );
    }
}

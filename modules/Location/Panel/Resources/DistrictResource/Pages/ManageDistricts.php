<?php

namespace Modules\Location\Panel\Resources\DistrictResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;
use Modules\Location\Panel\Resources\DistrictResource;
use Modules\Location\Panel\Resources\DistrictResource\Enums\DistrictPermission;

class ManageDistricts extends ManageRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
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

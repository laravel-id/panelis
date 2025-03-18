<?php

namespace App\Filament\Resources\Location\RegionResource\Pages;

use App\Filament\Resources\Location\RegionResource;
use App\Filament\Resources\Location\RegionResource\Enums\RegionPermission;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Symfony\Component\HttpFoundation\Response;

class ManageRegions extends ManageRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(user_can(RegionPermission::Add)),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);

        abort_unless(
            user_can(RegionPermission::Browse) && user_can(RegionPermission::Add),
            Response::HTTP_FORBIDDEN,
        );
    }
}

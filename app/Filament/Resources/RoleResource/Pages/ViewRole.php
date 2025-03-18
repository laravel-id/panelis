<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Enums\RolePermission;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Http\Response;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(user_can(RolePermission::Edit)),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(RolePermission::Read), Response::HTTP_FORBIDDEN);
    }
}

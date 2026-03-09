<?php

namespace Modules\User\Panel\Resources\RoleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Http\Response;
use Modules\User\Panel\Resources\RoleResource;
use Modules\User\Panel\Resources\RoleResource\Enums\RolePermission;

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

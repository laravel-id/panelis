<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Enums\UserPermission;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Symfony\Component\HttpFoundation\Response;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(user_can(UserPermission::Edit)),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(UserPermission::Read), Response::HTTP_FORBIDDEN);
    }
}

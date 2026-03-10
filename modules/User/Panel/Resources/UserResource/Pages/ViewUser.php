<?php

namespace Modules\User\Panel\Resources\UserResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\User\Panel\Resources\UserResource;
use Modules\User\Panel\Resources\UserResource\Enums\UserPermission;
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

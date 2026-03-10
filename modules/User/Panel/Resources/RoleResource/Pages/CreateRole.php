<?php

namespace Modules\User\Panel\Resources\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Response;
use Modules\User\Panel\Resources\RoleResource;
use Modules\User\Panel\Resources\RoleResource\Enums\RolePermission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(RolePermission::Create), Response::HTTP_FORBIDDEN);
    }
}

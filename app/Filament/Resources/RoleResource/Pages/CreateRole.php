<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Enums\RolePermission;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Response;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(RolePermission::Add), Response::HTTP_FORBIDDEN);
    }
}

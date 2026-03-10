<?php

namespace Modules\User\Panel\Resources\RoleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Modules\User\Panel\Resources\RoleResource;
use Modules\User\Panel\Resources\RoleResource\Enums\RolePermission;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(RolePermission::Create)),
        ];
    }

    public function authorizeAccess(): void
    {
        abort_unless(user_can(RolePermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

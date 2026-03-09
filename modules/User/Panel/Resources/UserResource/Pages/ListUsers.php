<?php

namespace Modules\User\Panel\Resources\UserResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Modules\User\Panel\Resources\UserResource;
use Modules\User\Panel\Resources\UserResource\Enums\UserPermission;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->visible(user_can(UserPermission::Create)),
        ];
    }

    public function authorizeAccess(): void
    {
        abort_unless(user_can(UserPermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

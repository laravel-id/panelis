<?php

namespace Modules\User\Panel\Resources\UserResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Response;
use Modules\User\Panel\Resources\UserResource;
use Modules\User\Panel\Resources\UserResource\Enums\UserPermission;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(UserPermission::Edit), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->visible(user_can(UserPermission::Read)),
            DeleteAction::make()->visible(user_can(UserPermission::Delete)),
        ];
    }
}

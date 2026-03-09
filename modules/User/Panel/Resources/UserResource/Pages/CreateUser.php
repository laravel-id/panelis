<?php

namespace Modules\User\Panel\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Modules\User\Actions\SendResetPasswordLink;
use Modules\User\Panel\Resources\UserResource;
use Modules\User\Panel\Resources\UserResource\Enums\UserPermission;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(UserPermission::Create), Response::HTTP_FORBIDDEN);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = ! empty($data['password']) ? Hash::make($data['password']) : '';
        unset($data['send_reset_password_link'], $data['password_confirmation']);

        return $data;
    }

    public function afterCreate(): void
    {
        if (empty($this->data['password'])) {
            SendResetPasswordLink::run($this->getRecord());
        }
    }
}

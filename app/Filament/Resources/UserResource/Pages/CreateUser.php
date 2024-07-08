<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Actions\User\SendResetPasswordLink;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = '';

        return $data;
    }

    public function afterCreate(): void
    {
        if (data_get($this->data, 'send_reset_password_link', true)) {
            SendResetPasswordLink::run($this->getRecord());
        }
    }
}

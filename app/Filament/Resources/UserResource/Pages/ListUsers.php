<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Enums\UserPermission;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->visible(user_can(UserPermission::Add)),
        ];
    }

    public function authorizeAccess(): void
    {
        abort_unless(user_can(UserPermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

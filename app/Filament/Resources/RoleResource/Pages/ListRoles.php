<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Enums\RolePermission;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(RolePermission::Add)),
        ];
    }

    public function authorizeAccess(): void
    {
        abort_unless(user_can(RolePermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

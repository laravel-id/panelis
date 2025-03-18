<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Resources\ModuleResource;
use App\Filament\Resources\ModuleResource\Enums\ModulePermission;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(): void
    {
        abort_unless(user_can(ModulePermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

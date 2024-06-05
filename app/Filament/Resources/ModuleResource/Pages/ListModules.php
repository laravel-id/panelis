<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Resources\ModuleResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(): void
    {
        abort_unless(Auth::user()->can('Manage module'), Response::HTTP_FORBIDDEN);
    }
}

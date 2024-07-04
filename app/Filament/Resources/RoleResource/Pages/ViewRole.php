<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(Auth::user()->can('UpdateRole')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('ViewRole'), 403);
    }
}

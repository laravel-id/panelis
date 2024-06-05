<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(Auth::user()->can('View user')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('View user'), 403);
    }
}

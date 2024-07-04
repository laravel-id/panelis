<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(Auth::user()->can('ViewUser')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('ViewUser'), Response::HTTP_FORBIDDEN);
    }
}

<?php

namespace App\Filament\Resources\Misc\TodoResource\Pages;

use App\Filament\Resources\Misc\TodoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageTodos extends ManageRecords
{
    protected static string $resource = TodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(Auth::user()->can('Create todo')),
        ];
    }
}

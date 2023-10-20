<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->visible(Auth::user()->can('View blog category')),
            Actions\DeleteAction::make()->visible(Auth::user()->can('Delete blog category')),
            Actions\ForceDeleteAction::make()->visible(Auth::user()->can('Delete blog category')),
            Actions\RestoreAction::make()->visible(Auth::user()->can('Update blog category')),
        ];
    }
}

<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Response;
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

    protected function authorizeAccess(): void
    {
        abort_unless(config('modules.blog'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('Update blog category'), Response::HTTP_FORBIDDEN);
    }
}

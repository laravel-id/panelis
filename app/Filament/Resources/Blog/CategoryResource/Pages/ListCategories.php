<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(Auth::user()->can('Create blog category')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('modules.blog'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('View blog category'), Response::HTTP_FORBIDDEN);
    }
}

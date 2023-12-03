<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(Auth::user()->can('CreateBlogCategory')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog'), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('ViewBlogCategory'), Response::HTTP_FORBIDDEN);
    }
}

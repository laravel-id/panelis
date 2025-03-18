<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\Response;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(CategoryPermission::Add)),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(user_can(CategoryPermission::Browse), Response::HTTP_FORBIDDEN);
    }
}

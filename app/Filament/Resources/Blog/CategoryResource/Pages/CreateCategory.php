<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use Filament\Resources\Pages\CreateRecord;
use Symfony\Component\HttpFoundation\Response;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(user_can(CategoryPermission::Add), Response::HTTP_FORBIDDEN);
    }
}

<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('CreateBlogCategory'), Response::HTTP_FORBIDDEN);
    }
}

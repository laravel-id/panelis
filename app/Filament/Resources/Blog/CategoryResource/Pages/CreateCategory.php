<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('modules.blog'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('Create blog category'), Response::HTTP_FORBIDDEN);
    }
}

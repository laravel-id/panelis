<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Filament\Resources\Blog\PostResource;
use App\Filament\Resources\Blog\PostResource\Enums\PostPermission;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(user_can(PostPermission::Add), Response::HTTP_FORBIDDEN);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['content'] = ! empty($data['content']) ? $data['content'] : '';
        $data['published_at'] = ! empty($data['published_at']) ? $data['published_at'] : now();

        return $data;
    }
}

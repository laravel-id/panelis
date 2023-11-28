<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Events\Blog\PostDeleted;
use App\Filament\Resources\Blog\PostResource;
use App\Models\Blog\Post;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('modules.blog'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('Update blog post'), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        $canDelete = Auth::user()->can('Delete blog post');

        return [
            Actions\DeleteAction::make()->visible($canDelete),
            Actions\ForceDeleteAction::make()
                ->visible($canDelete)
                ->requiresConfirmation()
                ->after(fn(?Post $post) => event(new PostDeleted($post))),
            Actions\RestoreAction::make()->visible(Auth::user()->can('Update blog post')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['content'] = !empty($data['content']) ? $data['content'] : '';

        return $data;
    }
}

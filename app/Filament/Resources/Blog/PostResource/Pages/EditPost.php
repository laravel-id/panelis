<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Events\Blog\PostDeleted;
use App\Filament\Resources\Blog\PostResource;
use App\Models\Blog\Post;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('UpdateBlogPost'), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        $canDelete = Auth::user()->can('DeleteBlogPost');

        return [
            DeleteAction::make()
                ->visible($canDelete),

            ActionGroup::make([
                ForceDeleteAction::make()
                    ->visible($canDelete)
                    ->requiresConfirmation()
                    ->after(fn (Post $post) => event(new PostDeleted($post))),

                RestoreAction::make()->visible(Auth::user()->can('UpdateBlogPost')),
            ]),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['content'] = ! empty($data['content']) ? $data['content'] : '';

        return $data;
    }
}

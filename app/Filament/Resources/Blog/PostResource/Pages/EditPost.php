<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Events\Blog\PostDeleted;
use App\Filament\Resources\Blog\PostResource;
use App\Filament\Resources\Blog\PostResource\Enums\PostPermission;
use App\Models\Blog\Post;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(user_can(PostPermission::Edit), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(user_can(PostPermission::Delete)),

            ActionGroup::make([
                ForceDeleteAction::make()
                    ->visible(user_can(PostPermission::Delete))
                    ->requiresConfirmation()
                    ->after(fn (Post $post) => event(new PostDeleted($post))),

                RestoreAction::make()->visible(user_can(PostPermission::Edit)),
            ]),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['content'] = ! empty($data['content']) ? $data['content'] : '';

        return $data;
    }
}

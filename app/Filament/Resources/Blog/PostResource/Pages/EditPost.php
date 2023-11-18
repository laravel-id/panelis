<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Events\Blog\PostDeleted;
use App\Filament\Resources\Blog\PostResource;
use App\Models\Blog\Post;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make()
                ->requiresConfirmation()
                ->after(fn(?Post $post) => event(new PostDeleted($post))),
            Actions\RestoreAction::make(),
        ];
    }
}

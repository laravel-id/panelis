<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\Response;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->visible(user_can(CategoryPermission::Read)),

            ActionGroup::make([
                DeleteAction::make()
                    ->visible(user_can(CategoryPermission::Delete)),

                ForceDeleteAction::make()
                    ->visible(user_can(CategoryPermission::Delete)),
            ]),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog', false), Response::HTTP_NOT_FOUND);

        abort_unless(user_can(CategoryPermission::Edit), Response::HTTP_FORBIDDEN);
    }
}

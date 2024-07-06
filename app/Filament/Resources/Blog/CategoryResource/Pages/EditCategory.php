<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->visible(Auth::user()
                ->can('ViewBlogCategory')),

            Actions\ActionGroup::make([
                Actions\DeleteAction::make()
                    ->visible(Auth::user()->can('DeleteBlogCategory')),

                Actions\ForceDeleteAction::make()
                    ->visible(Auth::user()->can('DeleteBlogCategory')),
            ]),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog'), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('UpdateBlogCategory'), Response::HTTP_FORBIDDEN);
    }
}

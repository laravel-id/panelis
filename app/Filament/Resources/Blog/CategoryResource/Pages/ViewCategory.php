<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use App\Models\Blog\Category;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(Auth::user()
                ->can('ViewBlogCategory')),

            ActionGroup::make([
                DeleteAction::make()
                    ->visible(Auth::user()->can('DeleteBlogCategory')),

                ForceDeleteAction::make()
                    ->visible(Auth::user()->can('DeleteBlogCategory')),
            ]),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('module.blog'), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('ViewBlogCategory'), Response::HTTP_FORBIDDEN);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('blog.category_name'))
                            ->size(TextEntrySize::Large),

                        TextEntry::make('slug')
                            ->label(__('blog.category_slug'))
                            ->size(TextEntrySize::Large),

                        TextEntry::make('description')
                            ->label(__('blog.category_description'))
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('local_created_at')
                            ->label(__('ui.created_at')),

                        TextEntry::make('local_updated_at')
                            ->label(__('ui.updated_at')),

                        TextEntry::make('local_deleted_at')
                            ->label(__('ui.deleted_at'))
                            ->hidden(fn (Category $category): bool => empty($category->local_deleted_at)),
                    ]),
            ]);
    }
}

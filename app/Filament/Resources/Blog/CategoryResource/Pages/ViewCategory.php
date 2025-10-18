<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use App\Models\Blog\Category;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Symfony\Component\HttpFoundation\Response;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(user_can(CategoryPermission::Edit)),

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

        abort_unless(user_can(CategoryPermission::Read), Response::HTTP_FORBIDDEN);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('blog.category.title'))
                            ->size(TextSize::Large),

                        TextEntry::make('slug')
                            ->label(__('ui.slug'))
                            ->size(TextSize::Large),

                        TextEntry::make('description')
                            ->label(__('blog.category.description'))
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

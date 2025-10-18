<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\PostResource\Enums\PostPermission;
use App\Filament\Resources\Blog\PostResource\Forms\PostForm;
use App\Filament\Resources\Blog\PostResource\Pages\CreatePost;
use App\Filament\Resources\Blog\PostResource\Pages\EditPost;
use App\Filament\Resources\Blog\PostResource\Pages\ListPosts;
use App\Models\Blog\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('blog.post.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('blog.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('blog.post.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(PostPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.blog', false) && self::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components(PostForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_visible')
                    ->visible(user_can(PostPermission::Edit))
                    ->label(__('blog.post.is_published')),

                TextColumn::make('title')
                    ->label(__('blog.post.title')),

                TextColumn::make('categories.name')
                    ->label(__('blog.post.categories')),

                TextColumn::make('user.name')
                    ->label(__('blog.post.author')),

                TextColumn::make('published_at')
                    ->label(__('ui.published_at'))
                    ->since(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label(__('blog.post.is_visible')),

                SelectFilter::make('user_id')
                    ->label(__('blog.post.authors'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->visible(user_can(PostPermission::Edit)),
                DeleteAction::make()->visible(user_can(PostPermission::Delete)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(user_can(PostPermission::Delete)),
                    RestoreBulkAction::make()->visible(user_can(PostPermission::Edit)),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

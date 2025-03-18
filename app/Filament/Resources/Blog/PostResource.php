<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\PostResource\Enums\PostPermission;
use App\Filament\Resources\Blog\PostResource\Forms\PostForm;
use App\Filament\Resources\Blog\PostResource\Pages\CreatePost;
use App\Filament\Resources\Blog\PostResource\Pages\EditPost;
use App\Filament\Resources\Blog\PostResource\Pages\ListPosts;
use App\Models\Blog\Post;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreBulkAction;
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
        return __('blog.post');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.blog');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.blog_post');
    }

    public static function canAccess(): bool
    {
        return user_can(PostPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.blog', false) && self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(PostForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_visible')
                    ->visible(user_can(PostPermission::Edit))
                    ->label(__('blog.post_is_published')),

                TextColumn::make('title')
                    ->label(__('blog.post_title')),

                TextColumn::make('categories.name')
                    ->label(__('blog.post_categories')),

                TextColumn::make('user.name')
                    ->label(__('blog.post_author')),

                TextColumn::make('published_at')
                    ->label(__('ui.published_at'))
                    ->since(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label(__('blog.post_is_visible')),

                SelectFilter::make('user_id')
                    ->label(__('blog.post_authors'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()->visible(user_can(PostPermission::Edit)),
                DeleteAction::make()->visible(user_can(PostPermission::Delete)),
            ])
            ->bulkActions([
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

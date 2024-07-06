<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\PostResource\Forms\PostForm;
use App\Filament\Resources\Blog\PostResource\Pages\CreatePost;
use App\Filament\Resources\Blog\PostResource\Pages\EditPost;
use App\Filament\Resources\Blog\PostResource\Pages\ListPosts;
use App\Models\Blog\Post;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

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

    public static function canAccess(): bool
    {
        return Auth::user()->can('ViewBlogPost');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.blog');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(PostForm::make());
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('UpdateBlogPost');
        $canDelete = Auth::user()->can('DeleteBlogPost');

        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->visible($canUpdate)
                    ->label(__('blog.post_is_published')),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('blog.post_title')),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('blog.post_categories')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('blog.post_author')),

                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('ui.published_at'))
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label(__('blog.post_is_visible')),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('blog.post_authors'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible($canUpdate),
                Tables\Actions\DeleteAction::make()->visible($canDelete),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible($canDelete),
                    Tables\Actions\RestoreBulkAction::make()->visible($canUpdate),
                ])->visible($canUpdate || $canDelete),
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

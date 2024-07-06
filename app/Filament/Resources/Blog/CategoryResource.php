<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\CategoryResource\Forms\CategoryForm;
use App\Filament\Resources\Blog\CategoryResource\Pages;
use App\Models\Blog\Category;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('blog.category');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.blog');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.blog');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->can('ViewBlogCategory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make()
                    ->columnSpan(function (?Model $record): int {
                        return empty($record) ? 3 : 2;
                    })
                    ->columns()
                    ->schema(CategoryForm::make()),

                Section::make()
                    ->columnSpan(1)
                    ->hiddenOn(Pages\CreateCategory::class)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('ui.created_at')
                            ->content(fn (Category $category): string => $category->created_at),

                        Placeholder::make('updated_at')
                            ->label('ui.updated_at')
                            ->content(fn (Category $category): string => $category->updated_at),

                        Placeholder::make('deleted_at')
                            ->label(__('ui.deleted_at'))
                            ->hidden(fn (?Model $record): bool => empty($record->deleted_at))
                            ->content(fn (?Model $record): string => $record->deleted_at ?? ''),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canEdit = Auth::user()->can('EditBlogCategory');
        $canDelete = Auth::user()->can('DeleteBlogCategory');

        return $table
            ->recordUrl(function (Category $category): string {
                return Pages\ViewCategory::getUrl(['record' => $category]);
            })
            ->columns([
                ToggleColumn::make('is_visible')
                    ->label(__('blog.category_is_visible'))
                    ->visible($canEdit),

                TextColumn::make('slug')
                    ->label(__('blog.category_slug'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('blog.category_title'))
                    ->description(fn (Category $category): ?string => Str::words($category->description, 8))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('blog.category_is_visible'),

                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->visible($canEdit),

                DeleteAction::make()
                    ->visible($canDelete),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible($canDelete),

                    ForceDeleteBulkAction::make()
                        ->visible($canDelete),

                    RestoreBulkAction::make()
                        ->visible(Auth::user()->can('RestoreBlogCategory')),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
            'view' => Pages\ViewCategory::route('/{record}'),
        ];
    }
}

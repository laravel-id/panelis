<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use App\Filament\Resources\Blog\CategoryResource\Forms\CategoryForm;
use App\Filament\Resources\Blog\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\Blog\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\Blog\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\Blog\CategoryResource\Pages\ViewCategory;
use App\Models\Blog\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static bool $isScopedToTenant = false;

    public static function getLabel(): ?string
    {
        return __('blog.category.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('blog.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('blog.category.navigation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('module.blog', false) && self::canAccess();
    }

    public static function canAccess(): bool
    {
        return user_can(CategoryPermission::Browse);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columnSpan(function (?Model $record): int {
                        return empty($record) ? 3 : 2;
                    })
                    ->columns()
                    ->schema(CategoryForm::schema()),

                Section::make()
                    ->columnSpan(1)
                    ->hiddenOn(CreateCategory::class)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('ui.created_at')
                            ->state(fn (Category $category): string => $category->created_at),

                        TextEntry::make('updated_at')
                            ->label('ui.updated_at')
                            ->state(fn (Category $category): string => $category->updated_at),

                        TextEntry::make('deleted_at')
                            ->label(__('ui.deleted_at'))
                            ->hidden(fn (?Model $record): bool => empty($record->deleted_at))
                            ->state(fn (?Model $record): string => $record->deleted_at ?? ''),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function (Category $category): string {
                return ViewCategory::getUrl(['record' => $category]);
            })
            ->columns([
                ToggleColumn::make('is_visible')
                    ->label(__('blog.category.is_visible'))
                    ->visible(user_can(CategoryPermission::Edit)),

                TextColumn::make('slug')
                    ->label(__('blog.category.slug'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('blog.category.title'))
                    ->description(fn (Category $category): ?string => Str::words($category->description, 8))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('local_updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('blog.category.is_visible'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(user_can(CategoryPermission::Edit)),

                DeleteAction::make()
                    ->visible(user_can(CategoryPermission::Delete)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(user_can(CategoryPermission::Delete)),

                    ForceDeleteBulkAction::make()
                        ->visible(user_can(CategoryPermission::Delete)),

                    RestoreBulkAction::make()
                        ->visible(user_can(CategoryPermission::Edit)),
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
            'view' => ViewCategory::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\CategoryResource\Pages;
use App\Models\Blog\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function getLabel(): ?string
    {
        return __('Category');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Blog');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-s-list-bullet';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View blog category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make()
                    ->columnSpan(function (?Model $record): int {
                        return empty($record) ? 3 : 2;
                    })
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->autofocus()
                            ->live(true)
                            ->afterStateUpdated(
                                fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))
                            )
                            ->minLength(3)
                            ->maxLength(100),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignorable: $form->getRecord())
                            ->minLength(3)
                            ->maxLength(150),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(5)
                            ->maxLength(250),

                        Forms\Components\Toggle::make('is_visible')
                            ->default(true)
                            ->translateLabel(),
                    ]),

                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->hiddenOn(Pages\CreateCategory::class)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn(?Model $record): string => $record->created_at),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn(?Model $record): string => $record->updated_at),

                        Forms\Components\Placeholder::make('deleted_at')
                            ->translateLabel()
                            ->hidden(fn(?Model $record): bool => empty($record->deleted_at))
                            ->content(fn(?Model $record): string => $record->deleted_at ?? ''),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canEdit = Auth::user()->can('blog_category_edit');
        $canDelete = Auth::user()->can('blog_category_delete');

        return $table
            ->recordUrl(function (?Model $record): string {
                return route('filament.admin.resources.blog.categories.view', $record);
            })
            ->columns([
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->words(8)
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->sortable()
                    ->tooltip(fn(?Model $record): string => $record->updated_at)
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible($canEdit),
                Tables\Actions\DeleteAction::make()
                    ->visible($canDelete),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible($canDelete),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible($canDelete),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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

<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\PostResource\Pages;
use App\Models\Blog\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getLabel(): ?string
    {
        return __('blog.post');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('blog.navigation');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('View blog post') && config('modules.blog');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make(__('blog.post'))
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('blog.fields.title'))
                            ->required()
                            ->minLength(3)
                            ->maxLength(250)
                            ->lazy()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state): void {
                                $set('slug', Str::slug($state));
                                $set('metadata', ['title' => $state]);
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('blog.fields.slug'))
                            ->required()
                            ->unique(ignorable: $form->getRecord())
                            ->minLength(3)
                            ->maxLength(250),

                        Forms\Components\MarkdownEditor::make('content')
                            ->label(__('blog.fields.content'))
                            ->columnSpanFull()
                            ->default(''),
                    ]),

                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('blog.fields.featured_image'))
                            ->moveFiles()
                            ->maxSize(5000)
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory(sprintf('blog/%s', now()->format('Y/m'))),

                        Forms\Components\Select::make('category_id')
                            ->label(__('blog.fields.category'))
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('blog.fields.title'))
                                    ->live(true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(250),

                                Forms\Components\TextInput::make('slug')
                                    ->label(__('blog.fields.slug'))
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(150),

                                Forms\Components\Textarea::make('description')
                                    ->label(__('blog.fields.description'))
                                    ->nullable()
                                    ->rows(5)
                                    ->maxLength(250),
                            ]),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('common.fields.published_at'))
                            ->nullable(),
                    ]),

                Forms\Components\Section::make(__('blog.additional_data'))
                    ->description(__('blog.set_custom_metadata'))
                    ->collapsed()
                    ->columnSpan(2)
                    ->reactive()
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->addActionLabel(__('blog.label.add_property'))
                            ->deletable(false)
                            ->addable(false)
                            ->default([
                                'title' => '',
                                'description' => '',
                                'keywords' => '',
                            ]),

                        Forms\Components\KeyValue::make('options')
                            ->label(__('blog.fields.options')),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        $canUpdate = Auth::user()->can('Update blog post');
        $canDelete = Auth::user()->can('Delete blog post');

        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->visible($canUpdate)
                    ->label(__('blog.fields.is_published')),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('blog.title')),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('blog.categories')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('blog.author')),

                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('blog.fields.published_at'))
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label(__('blog.fields.is_visible')),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('blog.authors'))
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
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

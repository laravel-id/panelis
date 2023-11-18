<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\PostResource\Pages;
use App\Filament\Resources\Blog\PostResource\RelationManagers;
use App\Models\Blog\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getLabel(): ?string
    {
        return __('Post');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Blog');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make(__('Post'))
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->minLength(3)
                            ->maxLength(250)
                            ->lazy()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state): void {
                                $set('slug', Str::slug($state));
                                $set('metadata', ['title' => $state]);
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->minLength(3)
                            ->maxLength(250),

                        Forms\Components\MarkdownEditor::make('content')
                            ->columnSpanFull()
                            ->default(''),
                    ]),

                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('Featured image'))
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
                            ->translateLabel()
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->live(true)
                                    ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(250),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(150),

                                Forms\Components\Textarea::make('description')
                                    ->nullable()
                                    ->rows(5)
                                    ->maxLength(250),
                            ]),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->nullable(),
                    ]),

                Forms\Components\Section::make(__('Additional data'))
                    ->description(__('Set custom metadata for post'))
                    ->collapsed()
                    ->columnSpan(2)
                    ->reactive()
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->translateLabel()
                            ->addActionLabel(__('Add property'))
                            ->deletable(false)
                            ->addable(false)
                            ->default([
                                'title' => '',
                                'description' => '',
                                'keywords' => '',
                            ]),

                        Forms\Components\KeyValue::make('options')
                            ->translateLabel()

                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label(__('Is published')),

                Tables\Columns\TextColumn::make('title')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('Categories')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Author')),

                Tables\Columns\TextColumn::make('published_at')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->translateLabel(),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('Authors'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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

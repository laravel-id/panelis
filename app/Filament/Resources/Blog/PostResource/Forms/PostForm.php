<?php

namespace App\Filament\Resources\Blog\PostResource\Forms;

use App\Filament\Resources\Blog\CategoryResource\Enums\CategoryPermission;
use App\Filament\Resources\Blog\CategoryResource\Forms\CategoryForm;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class PostForm
{
    public static function schema(): array
    {
        return [
            Section::make(__('blog.post.label'))
                ->columnSpan(2)
                ->schema([
                    TextInput::make('title')
                        ->label(__('blog.post.title'))
                        ->required()
                        ->minLength(3)
                        ->maxLength(250)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->label(__('blog.post.slug'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength(3)
                        ->maxLength(250),

                    MarkdownEditor::make('content')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->default(''),
                ]),

            Section::make()
                ->columnSpan(1)
                ->schema([
                    FileUpload::make('image')
                        ->label(__('blog.post.featured_image'))
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

                    Select::make('category_id')
                        ->label(__('blog.category.label'))
                        ->relationship('categories', 'name')
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->createOptionForm(user_can(CategoryPermission::Add) ? CategoryForm::schema() : null),

                    DateTimePicker::make('published_at')
                        ->label(__('ui.published_at'))
                        ->native(false)
                        ->nullable(),
                ]),

            Section::make(__('blog.post.additional_data'))
                ->collapsed()
                ->columnSpan(2)
                ->reactive()
                ->schema([
                    KeyValue::make('metadata')
                        ->addActionLabel(__('blog.btn.add_metadata'))
                        ->default([
                            'title' => '',
                            'description' => '',
                            'keywords' => '',
                        ]),
                ]),

        ];
    }
}

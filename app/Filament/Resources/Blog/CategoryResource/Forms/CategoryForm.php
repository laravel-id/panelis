<?php

namespace App\Filament\Resources\Blog\CategoryResource\Forms;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function make(): array
    {
        return [
            TextInput::make('name')
                ->label(__('blog.category_title'))
                ->required()
                ->autofocus()
                ->live(true)
                ->afterStateUpdated(
                    fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                )
                ->minLength(3)
                ->maxLength(100),

            TextInput::make('slug')
                ->label(__('blog.category_slug'))
                ->required()
                ->unique(ignoreRecord: true)
                ->minLength(3)
                ->maxLength(150),

            Textarea::make('description')
                ->label(__('blog.category_description'))
                ->columnSpanFull()
                ->rows(5)
                ->maxLength(250),

            Toggle::make('is_visible')
                ->label(__('blog.category_is_visible'))
                ->default(true),
        ];
    }
}

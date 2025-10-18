<?php

namespace App\Filament\Resources\Blog\CategoryResource\Forms;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('blog.category.title'))
                ->required()
                ->autofocus()
                ->live(true)
                ->afterStateUpdated(
                    fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                )
                ->minLength(3)
                ->maxLength(100),

            TextInput::make('slug')
                ->label(__('blog.category.slug'))
                ->required()
                ->unique(ignoreRecord: true)
                ->minLength(3)
                ->maxLength(150),

            Textarea::make('description')
                ->label(__('blog.category.description'))
                ->columnSpanFull()
                ->rows(5)
                ->maxLength(250),

            Toggle::make('is_visible')
                ->label(__('blog.category.is_visible'))
                ->default(true),
        ];
    }
}

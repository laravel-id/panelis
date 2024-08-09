<?php

namespace App\Filament\Resources\ChangelogResource\Forms;

use App\Filament\Resources\ChangelogResource\Enums\ChangelogType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class ChangelogForm
{
    public static function make(): array
    {
        return [
            TextInput::make('title')
                ->label(__('changelog.title'))
                ->dehydrateStateUsing(fn(?string $state): ?string => Str::apa($state ?? ''))
                ->required(),

            MarkdownEditor::make('description')
                ->label(__('changelog.description')),

            TextInput::make('url')
                ->label(__('changelog.url'))
                ->url()
                ->required(),

            TagsInput::make('types')
                ->label(__('changelog.types'))
                ->reorderable()
                ->suggestions(ChangelogType::cases())
                ->required(),

            DateTimePicker::make('logged_at')
                ->label(__('changelog.logged_at'))
                ->native(false)
                ->default(now())
                ->timezone(get_timezone())
                ->format(get_datetime_format())
                ->seconds(false)
                ->required(),
        ];
    }
}
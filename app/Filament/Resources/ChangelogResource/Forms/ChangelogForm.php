<?php

namespace App\Filament\Resources\ChangelogResource\Forms;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;

class ChangelogForm
{
    public static function make(): array
    {
        return [
            TextInput::make('title')
                ->label(__('changelog.title'))
                ->required(),

            MarkdownEditor::make('description')
                ->label(__('changelog.description')),

            Grid::make(2)
                ->schema([
                    TextInput::make('label')
                        ->label(__('changelog.url_label'))
                        ->required(),

                    TextInput::make('url')
                        ->label(__('changelog.url'))
                        ->url()
                        ->required(),
                ]),

            DateTimePicker::make('logged_at')
                ->label(__('changelog.logged_at'))
                ->native(false)
                ->timezone(get_timezone())
                ->format(get_datetime_format())
                ->seconds(false)
                ->required(),
        ];
    }
}
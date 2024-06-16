<?php

namespace App\Filament\Resources\Event\OrganizerResource\Forms;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OrganizerForm
{
    public static function schema(): array
    {
        return [
            Section::make(__('event.organizer_info'))
                ->columnSpan(1)
                ->schema([
                    FileUpload::make('logo')
                        ->hiddenLabel()
                        ->alignCenter()
                        ->disk('public')
                        ->directory('organizer')
                        ->visible('public')
                        ->moveFiles()
                        ->avatar()
                        ->nullable()
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                            return str($get('slug'))
                                ->append('.')
                                ->append($file->getClientOriginalExtension());
                        }),

                    Grid::make('organizer_name')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->columnSpan(2)
                                ->label(__('event.organizer_name'))
                                ->live(onBlur: true)
                                ->minLength(2)
                                ->maxLength(50)
                                ->required()
                                ->afterStateUpdated(function (?string $state, Set $set): void {
                                    if (! empty($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make('slug')
                                ->label(__('event.organizer_slug'))
                                ->unique(ignoreRecord: true)
                                ->required(),

                            ColorPicker::make('color')
                                ->label(__('event.organizer_brand_color'))
                                ->nullable()
                                ->hex(),

                        ]),

                    MarkdownEditor::make('description')
                        ->label(__('event.organizer_description'))
                        ->maxLength(1000),
                ]),

            Section::make(__('event.organizer_contact'))
                ->columnSpan(1)
                ->schema([
                    TextInput::make('phone')
                        ->label(__('event.organizer_phone'))
                        ->tel()
                        ->nullable(),

                    TextInput::make('email')
                        ->label(__('event.organizer_email'))
                        ->email()
                        ->nullable(),

                    TextInput::make('website')
                        ->label(__('event.organizer_website'))
                        ->url()
                        ->nullable(),

                    Textarea::make('address')
                        ->label(__('event.organizer_address'))
                        ->rows(3)
                        ->string()
                        ->nullable(),
                ]),
        ];
    }
}

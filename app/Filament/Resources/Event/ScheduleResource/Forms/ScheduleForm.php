<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Models\Location\District;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ScheduleForm
{
    public static function schema(): array
    {
        $timezone = config('app.datetime_timezone', config('app.timezone'));
        $locale = config('app.locale');

        return [
            Grid::make(__('event.schedule_what'))
                ->columns(1)
                ->schema([
                    Section::make(__('event.schedule_what'))
                        ->collapsible()
                        ->columnSpan(1)
                        ->columns(1)
                        ->schema([
                            FileUpload::make('poster')
                                ->hiddenLabel()
                                ->disk('public')
                                ->directory('poster')
                                ->visible('public')
                                ->moveFiles()
                                ->nullable(),

                            TextInput::make('title')
                                ->label(__('event.schedule_title'))
                                ->minLength(3)
                                ->maxLength(250)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    if (!empty($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->required(),

                            TextInput::make('slug')
                                ->label(__('event.schedule_slug'))
                                ->minLength(3)
                                ->maxLength(300)
                                ->required()
                                ->unique(ignoreRecord: true),

                            MarkdownEditor::make('description')
                                ->label(__('event.schedule_description'))
                                ->maxLength(1000),

                            TagsInput::make('categories')
                                ->label(__('event.schedule_category')),

                            TextInput::make('url')
                                ->label(__('event.schedule_url'))
                                ->url()
                                ->required(),
                        ]),

                    Section::make(__('event.schedule_when'))
                        ->collapsible()
                        ->schema([
                            DateTimePicker::make('started_at')
                                ->label(__('event.schedule_started_at'))
                                ->suffixIcon('heroicon-s-calendar')
                                ->timezone($timezone)
                                ->locale($locale)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->native(false)
                                ->live(onBlur: true)
                                ->required(),

                            DateTimePicker::make('finished_at')
                                ->label(__('event.schedule_finished_at'))
                                ->suffixIcon('heroicon-s-calendar')
                                ->timezone($timezone)
                                ->locale($locale)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->native(false)
                                ->minDate(fn(Get $get) => $get('started_at'))
                                ->nullable(),
                        ]),

                    Section::make(__('event.schedule_where'))
                        ->collapsible()
                        ->columns(1)
                        ->columnSpan(1)
                        ->schema([
                            Toggle::make('is_virtual')
                                ->label(__('event.schedule_is_virtual'))
                                ->live(),

                            TextInput::make('location')
                                ->label(__('event.schedule_location'))
                                ->hidden(fn(Get $get): bool => $get('is_virtual'))
                                ->required(),

                            Select::make('district_id')
                            ->label(__('event.schedule_district'))
                            ->searchable()
                                ->preload()
                            ->options(District::query()->pluck('name', 'id')),
                        ]),

                    Section::make(__('event.schedule_who'))
                        ->collapsible()
                        ->schema([
                            Repeater::make('contacts')
                                ->columns(3)
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('event.schedule_contact_name'))
                                        ->nullable(),

                                    TextInput::make('phone')
                                        ->label(__('event.schedule_contact_phone'))
                                        ->tel()
                                        ->required(),

                                    TextInput::make('email')
                                        ->label(__('event.schedule_contact_email'))
                                        ->email()
                                        ->nullable(),
                                ]),
                        ]),

                    KeyValue::make('metadata')
                        ->label(__('event.schedule_metadata')),
                ]),
        ];
    }
}

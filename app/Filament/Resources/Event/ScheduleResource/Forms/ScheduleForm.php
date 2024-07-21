<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Filament\Resources\Location\DistrictResource\Forms\DistrictForm;
use App\Models\Location\District;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ScheduleForm
{
    public static function schema(): array
    {
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
                                ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                    if (! empty($state) && $operation === 'create') {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->required(),

                            TextInput::make('slug')
                                ->label(__('event.schedule_slug'))
                                ->prefix(route('schedule.view', '').'/')
                                ->minLength(3)
                                ->maxLength(300)
                                ->required()
                                ->unique(ignoreRecord: true),

                            MarkdownEditor::make('description')
                                ->label(__('event.schedule_description'))
                                ->maxLength(5000),

                            TagsInput::make('categories')
                                ->label(__('event.schedule_category'))
                                ->splitKeys(['Tab', ' '])
                                ->reorderable()
                                ->suggestions([
                                    '5K',
                                    '10K',
                                    '15K',
                                    '21K',
                                    '42K',
                                ]),

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
                                ->timezone(get_timezone())
                                ->minutesStep(15)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->native(false)
                                ->live(onBlur: true)
                                ->required(),

                            DateTimePicker::make('finished_at')
                                ->label(__('event.schedule_finished_at'))
                                ->suffixIcon('heroicon-s-calendar')
                                ->timezone(get_timezone())
                                ->minutesStep(15)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->native(false)
                                ->minDate(fn (Get $get) => $get('started_at'))
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
                                ->hidden(fn (Get $get): bool => $get('is_virtual') ?? false)
                                ->required(),

                            Select::make('district_id')
                                ->label(__('event.schedule_district'))
                                ->relationship('district', 'name')
                                ->hidden(fn (Get $get): bool => $get('is_virtual') ?? false)
                                ->searchable()
                                ->preload()
                                ->createOptionForm(DistrictForm::make())
                                ->options(function (): array {
                                    return District::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }),

                            TextInput::make('metadata.location_url')
                                ->label(__('event.schedule_location_url'))
                                ->hidden(fn (Get $get): bool => $get('is_virtual'))
                                ->nullable()
                                ->url(),
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

                                    Checkbox::make('is_wa')
                                        ->label(__('event.schedule_contact_is_wa'))
                                        ->nullable(),
                                ]),
                        ]),

                    KeyValue::make('metadata')
                        ->label(__('event.schedule_metadata')),
                ]),
        ];
    }
}

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

                            Toggle::make('metadata.is_pinned')
                                ->label(__('event.schedule_pin')),

                            Select::make('parent_id')
                                ->label(__('event.schedule_parent'))
                                ->relationship('parent', 'title')
                                ->searchable()
                                ->preload(),

                            Grid::make(2)
                                ->schema([
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
                                        ->minLength(3)
                                        ->maxLength(300)
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                ]),

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
                                ->displayFormat(get_datetime_format())
                                ->suffixIcon('heroicon-s-calendar')
                                ->timezone(get_timezone())
                                ->minutesStep(15)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->default(now(get_timezone())->hour(5)->minute(0))
                                ->native(false)
                                ->maxDate(fn (Get $get): ?string => $get('finished_at'))
                                ->live(onBlur: true)
                                ->required(),

                            DateTimePicker::make('finished_at')
                                ->label(__('event.schedule_finished_at'))
                                ->displayFormat(get_datetime_format())
                                ->suffixIcon('heroicon-s-calendar')
                                ->timezone(get_timezone())
                                ->minutesStep(15)
                                ->closeOnDateSelection()
                                ->seconds(false)
                                ->native(false)
                                ->live(onBlur: true)
                                ->minDate(fn (Get $get): ?string => $get('started_at'))
                                ->nullable(),

                            Toggle::make('metadata.hide_time')
                                ->label(__('event.schedule_hide_time'))
                                ->default(true),
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
                                ->hidden(fn (Get $get): bool => $get('is_virtual') ?? false)
                                ->nullable()
                                ->url(),
                        ]),

                    Section::make(__('event.schedule_who'))
                        ->collapsible()
                        ->schema([
                            Repeater::make('contacts')
                                ->columns()
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('event.schedule_contact_name'))
                                        ->hiddenLabel()
                                        ->prefixIcon('heroicon-s-user')
                                        ->nullable(),

                                    TextInput::make('phone')
                                        ->label(__('event.schedule_contact_phone'))
                                        ->hiddenLabel()
                                        ->prefixIcon('heroicon-s-phone')
                                        ->live(onBlur: true)
                                        ->dehydrateStateUsing(function (?string $state): string {
                                            if (! empty($state)) {
                                                return Str::of($state)
                                                    ->trim()
                                                    ->remove('-')
                                                    ->remove(' ')
                                                    ->toString();
                                            }

                                            return '';
                                        })
                                        ->tel()
                                        ->required(),

                                    Checkbox::make('is_wa')
                                        ->label(__('event.schedule_contact_is_wa'))
                                        ->columnSpanFull()
                                        ->nullable(),
                                ]),
                        ]),

                    KeyValue::make('metadata')
                        ->label(__('event.schedule_metadata')),
                ]),
        ];
    }
}

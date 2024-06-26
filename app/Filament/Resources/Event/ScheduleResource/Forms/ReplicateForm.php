<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Models\Location\District;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ReplicateForm
{
    public static function make(): array
    {
        return [
            Section::make(__('event.schedule_what'))
                ->collapsed()
                ->schema([
                    TextInput::make('title')
                        ->label(__('event.schedule_title'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            if (! empty($state)) {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->required(),

                    TextInput::make('slug')
                        ->label(__('event.schedule_slug'))
                        ->unique()
                        ->required(),

                    TextInput::make('url')
                        ->label(__('event.schedule_url'))
                        ->url()
                        ->required(),
                ]),

            Section::make(__('event.schedule_when'))
                ->collapsed()
                ->schema([
                    DateTimePicker::make('started_at')
                        ->label(__('event.schedule_started_at'))
                        ->timezone(get_timezone())
                        ->minutesStep(15)
                        ->closeOnDateSelection()
                        ->seconds(false)
                        ->native(false)
                        ->live(onBlur: true)
                        ->date()
                        ->required(),

                    DateTimePicker::make('finished_at')
                        ->label(__('event.schedule_finished_at'))
                        ->native(false)
                        ->timezone(get_timezone())
                        ->minutesStep(15)
                        ->closeOnDateSelection()
                        ->seconds(false)
                        ->native(false)
                        ->date()
                        ->minDate(fn (Get $get) => $get('started_at')),
                ]),

            Section::make(__('event.schedule_where'))
                ->collapsed()
                ->schema([
                    Toggle::make('is_virtual')
                        ->label(__('event.schedule_is_virtual'))
                        ->live(),

                    TextInput::make('location')
                        ->label(__('event.schedule_location'))
                        ->hidden(fn (Get $get): bool => $get('is_virtual'))
                        ->required(fn (Get $get): bool => ! $get('is_virtual')),

                    Select::make('district_id')
                        ->label(__('event.schedule_district'))
                        ->searchable()
                        ->preload()
                        ->options(District::query()->pluck('name', 'id'))
                        ->hidden(fn (Get $get): bool => $get('is_virtual'))
                        ->required(fn (Get $get): bool => ! $get('is_virtual')),
                ]),

            Toggle::make('replicate_type')
                ->label(__('event.schedule_replicate_type'))
                ->default(true),

            Toggle::make('replicate_organizer')
                ->label(__('event.schedule_replicate_organizer'))
                ->default(true),

            Toggle::make('replicate_package')
                ->label(__('event.schedule_replicate_packages'))
                ->default(true),
        ];
    }
}

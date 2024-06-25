<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class PackageForm
{
    public static function schema(): array
    {
        $timezone = config('app.datetime_timezone', config('app.timezone'));
        $locale = config('app.locale');

        return [
            Repeater::make(__('event.package'))
                ->relationship('packages')
                ->columns(1)
                ->columns([
                    'md' => 1,
                    'lg' => 2,
                ])
                ->schema([
                    Grid::make()
                        ->columnSpan(2)
                        ->schema([
                            TextInput::make('title')
                                ->label(__('event.package_title'))
                                ->maxLength(250)
                                ->required(),

                            TextInput::make('price')
                                ->label(__('event.package_price'))
                                ->default(0)
                                ->numeric()
                                ->required(),
                        ]),

                    Grid::make()
                        ->columnSpan(2)
                        ->schema([
                            DatetimePicker::make('started_at')
                                ->label(__('event.package_started_at'))
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->timezone($timezone)
                                ->locale($locale)
                                ->live(onBlur: true)
                                ->nullable(),

                            DatetimePicker::make('ended_at')
                                ->label(__('event.package_ended_at'))
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->timezone($timezone)
                                ->locale($locale)
                                ->minDate(fn (Get $get): ?string => $get('started_at'))
                                ->nullable(),
                        ]),

                    Textarea::make('description')
                        ->label(__('event.package_description'))
                        ->rows(5)
                        ->columnSpan(2),
                ])
                ->orderColumn('sort')
                ->reorderable()
                ->reorderableWithButtons(true)
                ->columns(2),
        ];
    }
}

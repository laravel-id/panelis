<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Filament\Resources\Event\ScheduleResource\Enums\PackagePriceType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class PackageForm
{
    public static function schema(): array
    {
        return [
            Repeater::make(__('event.package'))
                ->relationship('packages')
                ->columns(1)
                ->columns([
                    'md' => 1,
                    'lg' => 2,
                ])
                ->schema([
                    Toggle::make('is_sold')
                        ->label(__('event.package_is_sold'))
                        ->columnSpanFull()
                        ->nullable(),

                    TextInput::make('title')
                        ->label(__('event.package_title'))
                        ->maxLength(250)
                        ->columnSpan(3)
                        ->required(),

                    Grid::make()
                        ->columnSpan(3)
                        ->schema([
                            DatetimePicker::make('started_at')
                                ->label(__('event.package_started_at'))
                                ->displayFormat(get_datetime_format())
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->timezone(get_timezone())
                                ->live(onBlur: true)
                                ->maxDate(fn (Get $get): ?string => $get('ended_at'))
                                ->nullable(),

                            DatetimePicker::make('ended_at')
                                ->label(__('event.package_ended_at'))
                                ->displayFormat(get_datetime_format())
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(30)
                                ->closeOnDateSelection()
                                ->timezone(get_timezone())
                                ->live(onBlur: true)
                                ->minDate(fn (Get $get): ?string => $get('started_at'))
                                ->nullable(),
                        ]),

                    TextInput::make('url')
                        ->label(__('event.package_url'))
                        ->columnSpan(3)
                        ->prefixIcon('heroicon-o-link')
                        ->url()
                        ->nullable(),

                    Fieldset::make(__('event.package_price'))
                        ->columns(3)
                        ->columnSpan(3)
                        ->schema([
                            Select::make('price_type')
                                ->label(__('event.package_price_type'))
                                ->options(PackagePriceType::options())
                                ->default(PackagePriceType::Normal->value)
                                ->enum(PackagePriceType::class)
                                ->required(),

                            TextInput::make('price')
                                ->label(__('event.package_price'))
                                ->default(0)
                                ->numeric()
                                ->required(),

                            TextInput::make('quota')
                                ->label(__('event.package_quota'))
                                ->default(0)
                                ->numeric()
                                ->required(),
                        ]),

                    MarkdownEditor::make('description')
                        ->label(__('event.package_description'))
                        ->columnSpan(3),
                ])
                ->orderColumn('sort')
                ->reorderable()
                ->cloneable()
                ->collapsible()
                ->reorderableWithButtons(true)
                ->columns(2),
        ];
    }
}

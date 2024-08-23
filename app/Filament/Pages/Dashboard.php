<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('started_at')
                        ->label(__('widget.start_date'))
                        ->displayFormat(get_datetime_format())
                        ->timezone(get_timezone())
                        ->seconds(false)
                        ->maxDate(fn (Get $get): ?string => $get('end_date') ?? now())
                        ->closeOnDateSelection()
                        ->live()
                        ->native(false),

                    DatePicker::make('ended_at')
                        ->label(__('widget.end_date'))
                        ->displayFormat(get_datetime_format())
                        ->timezone(get_timezone())
                        ->seconds(false)
                        ->maxDate(now())
                        ->live()
                        ->minDate(fn (Get $get): ?string => $get('start_date'))
                        ->closeOnDateSelection()
                        ->native(false),
                ]),
        ];
    }
}

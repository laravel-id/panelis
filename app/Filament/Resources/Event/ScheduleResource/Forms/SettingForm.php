<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class SettingForm
{
    public static function make(): array
    {
        return [
            Toggle::make('metadata.registration')
                ->label(__('event.enable_registration'))
                ->live(),

            Select::make('metadata.bank_id')
                ->label(__('event.schedule_bank'))
                ->disabled(fn (Get $get): bool => ! $get('metadata.registration')),
        ];
    }
}

<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        return $data;
    }
}

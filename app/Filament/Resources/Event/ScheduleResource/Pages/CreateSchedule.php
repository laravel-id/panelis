<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Events\Event\ScheduleCreated;
use App\Filament\Resources\Event\ScheduleResource;
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

    protected function afterCreate(): void
    {
        event(new ScheduleCreated($this->record));
    }
}

<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Events\Event\ScheduleCreated;
use App\Filament\Resources\Event\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        $data['alias'] = Str::ascii($data['title']);

        return $data;
    }

    protected function afterCreate(): void
    {
        event(new ScheduleCreated($this->record));
    }
}

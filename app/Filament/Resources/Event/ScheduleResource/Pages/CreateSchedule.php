<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use AshAllenDesign\ShortURL\Facades\ShortURL;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        ShortURL::destinationUrl($data['url'])
            ->trackVisits()
            ->make();

        return $data;
    }
}

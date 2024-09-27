<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Events\Event\ScheduleCreated;
use App\Filament\Resources\Event\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        if (empty($data['description'])) {
            $data['description'] = '';
        }

        if (! Str::isAscii($data['title'])) {
            $data['alias'] = Str::ascii($data['title']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // assign current user to newly created schedule
        Auth::user()->schedules()->attach($this->record);

        event(new ScheduleCreated($this->record));

        Cache::forget('event.pinned');
    }
}

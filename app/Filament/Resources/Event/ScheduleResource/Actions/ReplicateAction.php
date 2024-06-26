<?php

namespace App\Filament\Resources\Event\ScheduleResource\Actions;

use App\Events\Event\ScheduleCreated;
use App\Models\Event\Schedule;
use Illuminate\Support\Facades\DB;

class ReplicateAction
{
    public static function beforeReplicate(Schedule $schedule, Schedule $replica, array $data): void
    {
        DB::transaction(function () use ($schedule, $data, $replica): void {
            $schedule->load(['organizers', 'types', 'packages']);

            $replica->fill($data);
        });
    }

    public static function afterReplicate(Schedule $schedule, Schedule $replica, array $data): void
    {
        DB::transaction(function () use ($schedule, $replica, $data): void {
            if ($data['replicate_type']) {
                $replica->types()->attach($schedule->types);
            }

            if ($data['replicate_organizer']) {
                $replica->organizers()->attach($schedule->organizers);
            }

            if ($data['replicate_package']) {
                $replica->packages()->createMany($schedule->packages->toArray());
            }

        });

        event(new ScheduleCreated($replica));
    }
}

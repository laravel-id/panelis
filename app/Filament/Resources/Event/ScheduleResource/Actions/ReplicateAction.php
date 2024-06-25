<?php

namespace App\Filament\Resources\Event\ScheduleResource\Actions;

use App\Models\Event\Schedule;
use AshAllenDesign\ShortURL\Facades\ShortURL;
use Illuminate\Support\Facades\DB;

class ReplicateAction
{
    public static function beforeReplicate(Schedule $schedule, Schedule $replica, array $data): void
    {
        DB::transaction(function () use ($schedule, $data, $replica): void {
            $schedule->load(['organizers', 'types', 'packages']);

            $replica->fill($data);

            // create short URL
            ShortURL::destinationUrl($replica->url)
                ->trackVisits()
                ->make();
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
    }
}

<?php

namespace App\Actions\Schedule;

use App\Models\Event\Schedule;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\CalendarLinks\Link;

class GenerateCalendarUrl
{
    use AsAction;

    public function handle(Schedule $schedule): Link
    {
        $finishedAt = $schedule->finished_at;
        if (empty($schedule->finished_at)) {
            $finishedAt = $schedule->started_at;
        }

        return Link::create($schedule->title, $schedule->started_at, $finishedAt)
            ->address($schedule->location);
    }
}

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
        $startedAt = $schedule->started_at->timezone(get_timezone());
        $finishedAt = $schedule->finished_at?->timezone(get_timezone());
        if (empty($schedule->finished_at)) {
            $finishedAt = $startedAt->copy()->addHours(config('event.time_difference', 4));
        }

        $link = Link::create($schedule->title, $startedAt, $finishedAt);
        if ((! empty($schedule->location) && $schedule->location !== 'TBA') && ! $schedule->is_virtual) {
            $link->address($schedule->full_location);
        }

        return $link;
    }
}

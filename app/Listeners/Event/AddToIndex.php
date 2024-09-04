<?php

namespace App\Listeners\Event;

use App\Events\Event\ScheduleCreated;
use App\Events\Event\ScheduleUpdated;
use App\Models\Event\Event;
use App\Models\Event\Organizer;
use App\Models\Event\Type;

class AddToIndex
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScheduleCreated|ScheduleUpdated $event): void
    {
        $event->schedule->load(['types', 'organizers', 'district']);

        Event::query()->updateOrCreate(['id' => $event->schedule->id], array_merge(
            $event->schedule->only([
                'id',
                'slug',
                'title',
                'description',
                'categories',
                'started_at',
                'finished_at',
                'is_virtual',
            ]),
            [
                'location' => $event->schedule->full_location,
                'region' => $event->schedule->district?->region?->name,
                'types' => $event->schedule->types->map(fn (Type $type): ?string => $type->title),
                'organizers' => $event->schedule->organizers->map(fn (Organizer $organizer): ?string => $organizer->name),
            ],
        ));
    }
}

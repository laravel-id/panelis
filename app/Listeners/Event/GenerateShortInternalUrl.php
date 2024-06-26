<?php

namespace App\Listeners\Event;

use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;

class GenerateShortInternalUrl
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
    public function handle(object $event): void
    {

        // create short internal
        // ex: schedules.run/event/xxx -> schedules.run/xxx
        $slug = $event->schedule->slug;
        $shortUrl = ShortURL::findByKey($slug);

        if (empty($shortUrl)) {
            URLShortener::destinationUrl(route('schedule.view', $slug))
                ->urlKey($slug)
                ->make();
        }
    }
}

<?php

namespace App\Listeners\Event;

use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;

class GenerateShortExternalUrl
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
        URLShortener::destinationUrl($event->schedule->url)
            ->trackVisits()
            ->make();
    }
}

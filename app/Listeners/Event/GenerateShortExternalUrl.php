<?php

namespace App\Listeners\Event;

use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
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
     *
     * @throws ShortURLException
     */
    public function handle(object $event): void
    {
        URLShortener::destinationUrl($event->schedule->url)
            ->redirectStatusCode(302)
            ->trackVisits()
            ->make();
    }
}

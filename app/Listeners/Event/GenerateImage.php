<?php

namespace App\Listeners\Event;

use App\Facades\Schedule;

class GenerateImage
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
        Schedule::generateImage($event->schedule);
    }
}

<?php

namespace App\Listeners\Blog;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class DeleteImage
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
        if (!empty($event->post) && !empty($event->post->image)) {
            Storage::delete($event->post->image);
        }
    }
}

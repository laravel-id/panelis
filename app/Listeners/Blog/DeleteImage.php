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
        if (!empty($event->post?->image)) {;
            Storage::disk($event->post->image_location ?? 'public')
                ->delete($event->post->image);
        }
    }
}

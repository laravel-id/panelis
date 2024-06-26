<?php

namespace App\Facades\Event;

use App\Models\Event\Schedule as Model;
use Illuminate\Support\Facades\Storage;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Image;

class Schedule
{
    public function generateImage(Model $schedule): ?string
    {
        $storage = Storage::disk('public');
        $path = sprintf('events/%s.png', $schedule->slug);

        $image = (new Image())
            ->accentColor($schedule->metadata['color'] ?? '#76ABAE')
            ->border(BorderPosition::Top, width: 15)
            ->title($schedule->title)
            ->description($schedule->description)
            ->url(
                $schedule->started_at
                    ->timezone(get_timezone())
                    ->format('D, d F Y')
            )
            ->backgroundColor($schedule->metadata['bg_color'] ?? '#EEEEEE')
            ->toString();

        $storage->put($path, $image);

        return $storage->url($path);
    }
}

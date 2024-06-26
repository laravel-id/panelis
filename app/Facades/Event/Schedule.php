<?php

namespace App\Facades\Event;

use App\Models\Event\Schedule as Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Image;

class Schedule
{
    public function generateImage(Model $schedule): ?string
    {
        $dir = $schedule->started_at->timezone(get_timezone())->format('Y/m');
        $storage = Storage::disk('public');
        $path = sprintf('events/%s/%s.png', $dir, $schedule->slug);

        $image = (new Image())
            ->accentColor($schedule->metadata['color'] ?? '#76ABAE')
            ->border(BorderPosition::Top, width: 15)
            ->title(Str::words($schedule->title, 5))
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

    public function getImage(Model $schedule): ?string
    {
        $dir = $schedule->started_at->timezone(get_timezone())->format('Y/m');
        $storage = Storage::disk('public');
        $path = sprintf('events/%s/%s.png', $dir, $schedule->slug);

        if (! $storage->exists($path)) {
            $this->generateImage($schedule);
        }

        return $storage->url($path);
    }
}

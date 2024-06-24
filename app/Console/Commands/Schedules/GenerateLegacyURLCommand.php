<?php

namespace App\Console\Commands\Schedules;

use App\Models\Event\Schedule;
use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Illuminate\Console\Command;

class GenerateLegacyURLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-schedule-legacy-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redirect old legacy URL to a new one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Schedule::query()
            ->get()
            ->each(function (Schedule $schedule): void {
                $url = ShortURL::findByKey($schedule->slug);
                if (empty($url)) {
                    URLShortener::destinationUrl(route('schedule.view', $schedule->slug))
                        ->trackVisits(false)
                        ->urlKey($schedule->slug)
                        ->secure(app()->isProduction())
                        ->make();
                }
            });

        return 0;
    }
}

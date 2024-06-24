<?php

namespace App\Console\Commands\Schedules;

use App\Models\Event\Schedule;
use App\Models\ShortURL;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Illuminate\Console\Command;

class GenerateURLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-schedule-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate external URL from schedules into shorter internal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Schedule::all(['url'])->each(function (Schedule $schedule): void {
            $exists = ShortURL::query()
                ->where('destination_url', $schedule->url)
                ->exists();

            if (!$exists) {
                URLShortener::destinationUrl($schedule->url)
                    ->trackVisits()
                    ->make();
            }
        });


        return 0;
    }
}

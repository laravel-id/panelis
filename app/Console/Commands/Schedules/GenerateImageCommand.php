<?php

namespace App\Console\Commands\Schedules;

use App\Facades\Schedule as ScheduleFacade;
use App\Models\Event\Schedule;
use Illuminate\Console\Command;

class GenerateImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-schedule-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate opengraph image for schedules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->withProgressBar(Schedule::getPublishedSchedules(), function (Schedule $schedule): void {
            ScheduleFacade::getImage($schedule);
        });

        return 0;
    }
}

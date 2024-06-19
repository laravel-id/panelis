<?php

namespace App\Console;

use App\Filament\Clusters\Databases\Enums\DatabasePeriod;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:backup-database')
            ->withoutOverlapping()
            ->runInBackground()
            ->when(function (): bool {
                if (! config('database.auto_backup_enabled')) {
                    return false;
                }

                if (config('database.backup_period') === DatabasePeriod::Daily->value) {
                    [$hour, $minute] = explode(':', config('database.backup_time'), 2);
                    $time = Carbon::now(config('app.datetime_timezone'))
                        ->setHour(intval($hour))
                        ->setMinute(intval($minute))
                        ->setSecond(0);

                    return $time->isCurrentHour() && $time->isCurrentMinute();
                }

                return false;
            });

        $schedule->command('app:generate-sitemap')
            ->hourly()
            ->runInBackground();

        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

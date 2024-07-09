<?php

namespace App\Console;

use App\Actions\Ping;
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
        $schedule->command('app:ping')->hourly();

        $schedule->command('app:backup-database')
            ->withoutOverlapping()
            ->runInBackground()
            ->when(function (): bool {
                // ping first and then check the schedule
                Ping::run(config('healthcheck.database'));

                if (! config('database.auto_backup_enabled')) {
                    return false;
                }

                if (config('database.backup_period') === DatabasePeriod::Daily->value) {
                    [$hour, $minute] = explode(':', config('database.backup_time'), 2);
                    $time = Carbon::now(get_timezone())
                        ->setHour(intval($hour))
                        ->setMinute(intval($minute))
                        ->setSecond(0);

                    return $time->isCurrentHour() && $time->isCurrentMinute();
                }

                return false;
            });

        $schedule->command('telescope:prune --hours=48')->daily();

        $schedule->command('subscriber:send-schedule')->monthly();
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

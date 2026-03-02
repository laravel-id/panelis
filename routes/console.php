<?php

use App\Filament\Clusters\Databases\Enums\DatabasePeriod;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:backup-database')
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
    })->sendOutputTo(storage_path('logs/db.log'));

Artisan::command('app:ping', function (): void {
    Http::get(config('app.ping_url'));
})->when(fn (): bool => ! empty(config('app.ping_url')))->everyMinute();

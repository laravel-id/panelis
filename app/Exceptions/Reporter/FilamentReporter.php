<?php

namespace App\Exceptions\Reporter;

use App\Models\User;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FilamentReporter extends Exception
{
    public function handle(Throwable $e): void
    {
        if (! config('logging.enable_notification', false)) {
            return;
        }

        $key = sprintf('filament_error_%s', md5($e->getMessage()));

        if (Cache::has($key)) {
            return;
        }

        Cache::put($key, true, now()->addMinutes(5));

        $users = User::query()
            ->whereDoesntHave('roles')
            ->get();

        Notification::make('log')
            ->title(__('setting.log.label'))
            ->body($e->getMessage())
            ->danger()
            ->sendToDatabase($users);
    }
}

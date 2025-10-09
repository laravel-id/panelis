<?php

namespace App\Providers;

use App\Events\Branch\BranchRegistered;
use App\Events\Branch\BranchUpdated;
use App\Events\SettingUpdated;
use App\Listeners\Setting\FlushCache as FlushCacheSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        BranchRegistered::class => [

        ],
        BranchUpdated::class => [

        ],

        SettingUpdated::class => [
            FlushCacheSetting::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

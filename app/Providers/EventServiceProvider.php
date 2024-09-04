<?php

namespace App\Providers;

use App\Events\Event\ScheduleCreated;
use App\Events\Event\ScheduleUpdated;
use App\Events\SettingUpdated;
use App\Listeners\Event\AddToIndex;
use App\Listeners\Event\GenerateImage;
use App\Listeners\Event\GenerateShortExternalUrl;
use App\Listeners\Event\GenerateShortInternalUrl;
use App\Listeners\Setting\FlushCache as FlushCacheSetting;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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

        Login::class => [

        ],

        ScheduleCreated::class => [
            AddToIndex::class,
            GenerateShortExternalUrl::class,
            GenerateShortInternalUrl::class,
            GenerateImage::class,
        ],

        ScheduleUpdated::class => [
            AddToIndex::class,
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

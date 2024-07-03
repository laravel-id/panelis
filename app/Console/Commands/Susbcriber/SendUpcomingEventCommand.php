<?php

namespace app\Console\Commands\Susbcriber;

use App\Filament\Resources\SubscriberResource\Enums\SubscriberPeriod;
use App\Mail\Schedules\UpcomingEventMail;
use App\Models\Event\Schedule;
use App\Models\Subscriber;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUpcomingEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriber:send-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send upcoming event to subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = CarbonImmutable::now(get_timezone());
        $next = $now->addMonth();

        $subject = __('event.mail_subject_monthly_update', [
            'month' => $now->format('F'),
            'year' => $now->format('Y'),
        ]);

        Subscriber::getActiveSubscribers(SubscriberPeriod::Monthly)
            ->each(function (Subscriber $subscriber) use ($now, $next, $subject) {
                $schedules = [
                    'current' => Schedule::getFilteredSchedules($now->year, $now->month),
                    'next' => Schedule::getFilteredSchedules($next->year, $next->month),
                ];

                Mail::to($subscriber->email)->send(new UpcomingEventMail(
                    $subscriber,
                    $schedules,
                    $subject,
                ));
            });
    }
}

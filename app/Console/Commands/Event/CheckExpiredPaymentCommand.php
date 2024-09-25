<?php

namespace App\Console\Commands\Event;

use App\Enums\Events\PaymentStatus;
use App\Models\Event\Payment;
use Illuminate\Console\Command;

class CheckExpiredPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:payment-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment status and update to expired when more than time limit';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Payment::query()
            ->where('expired_at', '<', now())
            ->where('status', PaymentStatus::Pending)
            ->get()
            ->each(function (Payment $payment): void {
                $payment->status = PaymentStatus::Expired;
                $payment->save();
            });

        return self::SUCCESS;
    }
}

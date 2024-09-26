<?php

namespace App\Jobs\Transaction;

use App\Actions\Events\Participants\ConfirmPayment;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Event\Participant;
use App\Models\Transaction\Bank;
use App\Models\Transaction\Transaction;
use App\Services\Payments\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMootaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $mutation)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bank = Bank::query()
            ->where('vendor', Vendor::Moota->value)
            ->where('vendor_id', $this->mutation['bank_id'])
            ->firstOrFail();

        $transaction = Transaction::query()
            ->where('bank_id', $bank->id)
            ->where('vendor', Vendor::Moota->value)
            ->where('total', $this->mutation['amount'])
            ->where('status', TransactionStatus::Pending)
            ->firstOrFail();

        if ($transaction->transactionable instanceof Participant) {
            ConfirmPayment::run($transaction->transactionable);
        }

    }
}

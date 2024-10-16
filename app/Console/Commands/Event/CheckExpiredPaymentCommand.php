<?php

namespace App\Console\Commands\Event;

use App\Enums\Participants\Status;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Transaction\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:transaction-expired';

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
        Transaction::query()
            ->where('expired_at', '<', now())
            ->where('status', TransactionStatus::Pending)
            ->with('transactionable')
            ->get()
            ->each(function (Transaction $transaction): void {
                DB::transaction(function () use ($transaction): void {
                    $transaction->status = TransactionStatus::Expired;
                    $transaction->save();

                    $transaction->transactionable->status = Status::Expired;
                    $transaction->transactionable->save();
                });
            });

        return self::SUCCESS;
    }
}

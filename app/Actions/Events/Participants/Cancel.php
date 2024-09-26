<?php

namespace App\Actions\Events\Participants;

use App\Enums\Participants\Status;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Event\Participant;
use App\Services\Payments\Payment;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class Cancel
{
    use AsAction;

    public function handle(Participant $participant): void
    {
        DB::transaction(function () use ($participant): void {
            $participant->status = Status::Canceled;
            $participant->save();

            $participant->transaction->status = TransactionStatus::Canceled;
            $participant->transaction->save();

            app(Payment::class)
                ->setProvider($participant->transaction->vendor)
                ->cancelPayment($participant->transaction->vendor_id);
        });
    }
}

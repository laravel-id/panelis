<?php

namespace App\Actions\Events\Participants;

use App\Enums\Participants\Status;
use App\Enums\Transaction\TransactionStatus;
use App\Mail\Participants\PaidMail;
use App\Models\Event\Participant;
use App\Notifications\Participants\PaidNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class ConfirmPayment
{
    use AsAction;

    public function handle(Participant $participant): void
    {
        DB::transaction(function () use ($participant): void {
            $participant->status = Status::Paid;
            $participant->save();

            $participant->transaction->status = TransactionStatus::Paid;
            $participant->transaction->save();

            if (! empty($participant->email)) {
                Mail::to($participant->email)
                    ->locale(data_get($participant->schedule->metadata, 'locale', app()->getLocale()))
                    ->send((new PaidMail($participant))->afterCommit());
            }

            Notification::send($participant->schedule->users, (new PaidNotification($participant))->afterCommit());
        });
    }
}

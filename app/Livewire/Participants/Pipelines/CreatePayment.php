<?php

namespace App\Livewire\Participants\Pipelines;

use App\Actions\Transaction\CreatePaymentUrl;
use App\Models\Event\Participant;
use Closure;

class CreatePayment
{
    public function __invoke(Participant $participant, Closure $next): Participant
    {
        CreatePaymentUrl::run($participant->transaction, __('event.payment_for_package', [
            'title' => $participant->schedule->title,
        ]));

        return $next($participant);
    }
}

<?php

namespace App\Livewire\Participants\Pipelines;

use App\Mail\Participants\RegisteredMail;
use App\Models\Event\Participant;
use Closure;
use Illuminate\Support\Facades\Mail;

class SendEmail
{
    public function __invoke(Participant $participant, Closure $next): Participant
    {
        if (! empty($participant->email)) {
            Mail::to($participant->email)
                ->locale(data_get($participant->schedule->metadata, 'locale', config('app.locale')))
                ->send((new RegisteredMail($participant))->afterCommit());
        }

        return $next($participant);
    }
}

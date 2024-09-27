<?php

namespace App\Livewire\Participants\Pipelines;

use App\Models\Event\Participant;
use App\Notifications\Participants\RegisteredNotification;
use Closure;
use Illuminate\Support\Facades\Notification;

class SendNotification
{
    public function __invoke(Participant $participant, Closure $next): Participant
    {
        Notification::send(
            notifiables: $participant->schedule->users,
            notification: (new RegisteredNotification($participant))->afterCommit(),
        );

        return $next($participant);
    }
}

<?php

namespace App\Livewire\Participants\Pipelines;

use App\Models\Event\Participant;
use Closure;

class CreateTransaction
{
    public function __invoke(Participant $participant, Closure $next): Participant
    {
        $transaction = $participant->transaction()->create([
            'bank_id' => data_get($participant->schedule->metadata, 'bank_id'),
            'total' => $participant->package->price,
            'expired_at' => now()->addMinutes(data_get($participant->schedule->metadata, 'expired_duration', 60)),
            'metadata' => [
                'redirect_url' => route('participant.status', $participant->ulid),
                'customer' => [
                    'name' => $participant->name,
                    'phone' => $participant->phone,
                    'email' => $participant->email,
                ],
            ],
        ]);

        $transaction->items()->create([
            'name' => $participant->package->title,
            'price' => $participant->package->price,
        ]);

        return $next($participant);
    }
}

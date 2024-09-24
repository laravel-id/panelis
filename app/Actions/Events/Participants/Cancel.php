<?php

namespace App\Actions\Events\Participants;

use App\Enums\Participants\Status;
use App\Models\Event\Participant;
use Lorisleiva\Actions\Concerns\AsAction;

class Cancel
{
    use AsAction;

    public function handle(Participant $participant): void
    {
        $participant->status = Status::Canceled;
        $participant->save();
    }
}

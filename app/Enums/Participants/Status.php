<?php

namespace App\Enums\Participants;

enum Status: string
{
    case Pending = 'pending';

    case Paid = 'paid';

    case Expired = 'expired';

    /**
     * Canceled manually by participant.
     */
    case Canceled = 'canceled';

    case Completed = 'completed';

    /**
     * Participant has been finished the event.
     */
    case Finished = 'finished';

    public function label(): string
    {
        return __(sprintf('event.participant_status_%s', $this->value));
    }
}

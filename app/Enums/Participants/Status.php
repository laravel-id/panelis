<?php

namespace App\Enums\Participants;

use App\Models\Enums\HasOption;

enum Status: string implements HasOption
{
    case Pending = 'pending';

    case Paid = 'paid';

    case Expired = 'expired';

    /**
     * Canceled manually by participant.
     */
    case Canceled = 'canceled';

    case OnHold = 'on_hold';

    case Completed = 'completed';

    /**
     * Participant has been finished the event.
     */
    case Finished = 'finished';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('event.participant_status_%s', $this->value));
    }
}

<?php

namespace App\Enums\Participants;

enum BloodType: string
{
    case A = 'a';

    case B = 'b';

    case AB = 'ab';

    case O = 'o';

    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this->value) {
            'unknown' => __('event.participant_blood_unknown'),
            default => strtoupper($this->value)
        };
    }
}

<?php

namespace App\Enums\Participants;

enum Gender: string
{
    case Male = 'm';

    case Female = 'f';

    public function label(): string
    {
        return __(sprintf('event.participant_gender_%s', $this->value));
    }
}

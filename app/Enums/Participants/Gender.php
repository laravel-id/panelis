<?php

namespace App\Enums\Participants;

use App\Models\Enums\HasOption;
use App\Models\Traits\HasOption as HasOptionTrait;

enum Gender: string implements HasOption
{
    use HasOptionTrait;

    case Male = 'm';

    case Female = 'f';

    public function label(): string
    {
        return __(sprintf('event.participant_gender_%s', $this->value));
    }
}

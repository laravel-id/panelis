<?php

namespace App\Enums\Participants;

use App\Models\Enums\HasOption;
use App\Models\Traits\HasOption as HasOptionTrait;

enum Relation: string implements HasOption
{
    use HasOptionTrait;

    case Parent = 'parent';

    case Child = 'child';

    case Spouse = 'spouse';

    case Other = 'other';

    public function label(): string
    {
        return __(sprintf('event.participant_relation_%s', $this->value));
    }
}

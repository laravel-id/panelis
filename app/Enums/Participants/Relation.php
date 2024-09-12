<?php

namespace App\Enums\Participants;

enum Relation: string
{
    case Parent = 'parent';

    case Child = 'child';

    case Spouse = 'spouse';

    case Other = 'other';

    public function label(): string
    {
        return __(sprintf('event.participant_relation_%s', $this->value));
    }
}

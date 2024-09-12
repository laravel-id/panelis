<?php

namespace App\Enums\Participants;

enum IdentityType: string
{
    case KTP = 'ktp';

    case SIM = 'sim';

    case Passport = 'passport';

    case KITAS = 'kitas';

    case StudentCard = 'student_card';

    case Other = 'other';

    public function label(): string
    {
        return __(sprintf('event.participant_identity_%s', $this->value));
    }
}

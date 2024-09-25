<?php

namespace App\Enums\Events;

enum PaymentStatus: string
{
    case Pending = 'pending';

    case Paid = 'paid';

    case Expired = 'expired';

    public function label(): string
    {
        return __(sprintf('event.payment_status_%s', $this->value));
    }
}

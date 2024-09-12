<?php

namespace App\Enums\Events;

enum PaymentStatus: string
{
    case Pending = 'pending';

    case Paid = 'paid';

    case Expired = 'expired';
}

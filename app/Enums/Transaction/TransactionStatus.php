<?php

namespace App\Enums\Transaction;

enum TransactionStatus: string
{
    case Pending = 'pending';

    case Paid = 'paid';

    case Expired = 'expired';

    case Canceled = 'canceled';

    public function label(): string
    {
        return __(sprintf('event.payment_status_%s', $this->value));
    }
}

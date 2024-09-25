<?php

namespace App\Services\Payments;

use App\Services\Payments\DTO\PaymentUrl;

interface Payment
{
    public function createPaymentUrl(PaymentUrl $paymentUrl): ?PaymentUrl;

    public function getRegisteredBanks(): array;
}

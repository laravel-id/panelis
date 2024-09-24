<?php

namespace App\Services\Payments;

use App\Services\Payments\DTO\PaymentLink;

interface Payment
{
    public function createPaymentLink(PaymentLink $paymentLink): array;
}

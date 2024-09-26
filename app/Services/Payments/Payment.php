<?php

namespace App\Services\Payments;

use App\Services\Payments\DTO\Bank;
use App\Services\Payments\DTO\PaymentUrl;

interface Payment
{
    public function getVendor(): string;

    public function createPaymentUrl(PaymentUrl $paymentUrl): ?PaymentUrl;

    public function getBanks(): array;

    public function createBankAccount(Bank $bank): ?Bank;

    public function getRegisteredBanks(): array;
}

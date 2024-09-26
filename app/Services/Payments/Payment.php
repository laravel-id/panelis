<?php

namespace App\Services\Payments;

use App\Services\Payments\DTO\Bank;
use App\Services\Payments\DTO\PaymentUrl;

interface Payment
{
    public function getVendor(): string;

    public function createPayment(PaymentUrl $paymentUrl): ?PaymentUrl;

    public function getPayment(string $id): ?PaymentUrl;

    public function cancelPayment(string $id): ?PaymentUrl;

    public function getBanks(): array;

    public function createBankAccount(Bank $bank): ?Bank;

    public function getRegisteredBanks(): array;
}

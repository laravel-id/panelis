<?php

namespace App\Actions\Transaction;

use App\Models\Transaction\Bank;
use App\Services\Payments\Payment;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchBank
{
    use AsAction;

    public function handle(): void
    {
        $payment = app(Payment::class);

        foreach ($payment->getRegisteredBanks() as $bank) {
            Bank::query()->updateOrCreate([
                'bank_code' => $bank->getCode(),
                'vendor_id' => $bank->getId(),
                'vendor' => $payment->getVendor(),
                'account_number' => $bank->getAccountNumber(),
            ], [
                'bank_name' => $bank->getLabel(),
                'account_name' => $bank->getAccountName(),
                'is_active' => $bank->isActive(),
                'balance' => $bank->getBalance(),
            ],
            );
        }
    }
}

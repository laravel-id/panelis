<?php

namespace App\Services\Payments\Vendors;

use App\Http\Integrations\Moota\MootaConnector;
use App\Http\Integrations\Moota\Requests\BankAccount;
use App\Http\Integrations\Moota\Requests\BankList;
use App\Http\Integrations\Moota\Requests\CreateBankAccount;
use App\Http\Integrations\Moota\Requests\ReceiveMoney;
use App\Services\Payments\DTO\Bank;
use App\Services\Payments\DTO\PaymentUrl;
use App\Services\Payments\Payment;
use App\Services\Payments\Vendor;
use Illuminate\Support\Facades\Log;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

readonly class Moota implements Payment
{
    public function __construct(private MootaConnector $connector) {}

    public function getVendor(): string
    {
        return Vendor::Moota->value;
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
     */
    public function createPaymentUrl(PaymentUrl $paymentUrl): ?PaymentUrl
    {
        $response = $this->connector->send(new ReceiveMoney([
            'order_id' => $paymentUrl->getId(),
            'bank_account_id' => $paymentUrl->getBankId(),
            'customers' => $paymentUrl->getCustomer(),
            'items' => $paymentUrl->getItems(),
            'description' => $paymentUrl->getDescription(),
            'note' => $paymentUrl->getNote(),
            'redirect_url' => $paymentUrl->getRedirectUrl(),
            'total' => $paymentUrl->getTotal(),
        ]));

        if ($response->failed()) {
            Log::warning('Failed to create payment link: '.$response->body());

            return null;
        }

        return (new PaymentUrl)
            ->setId('test-123')
            ->setVendor($this->getVendor())
            ->setPaymentUrl('https://app.moota.co/payment/PYM-xxxxx-xxxxx');

        return (new PaymentUrl)
            ->setId($response->json('order_id'))
            ->setBankId($response->json('bank_id'))
            ->setPaymentUrl($response->json('payment_url'));
    }

    public function getBanks(): array
    {
        $response = $this->connector->send(new BankList);

        $banks = [];
        foreach ($response->json('data', []) as $bank) {
            $banks[] = (new Bank)
                ->setCode($bank['id'])
                ->setLabel($bank['label']);
        }

        return $banks;
    }

    public function createBankAccount(Bank $bank): ?Bank
    {
        $response = $this->connector->send(new CreateBankAccount($bank));
        if ($response->failed()) {
            return null;
        }

        if (! $response->json('status')) {
            return null;
        }

        $bank = $response->json('bank');

        return (new Bank)
            ->setId($bank['bank_id'])
            ->setCode($bank['bank_type'])
            ->setLabel($bank['label'])
            ->setBalance((float) $bank['balance'])
            ->setAccountName($bank['atas_nama'])
            ->setAccountNumber($bank['account_number'])
            ->setIsActive((bool) $bank['is_active']);
    }

    public function getRegisteredBanks(): array
    {
        $response = $this->connector->send(new BankAccount);
        if ($response->failed()) {
            Log::warning('Failed to get registered banks: '.$response->body());

            return [];
        }

        $banks = [];
        foreach ($response->json('data', []) as $bank) {
            $banks[] = (new Bank)
                ->setId($bank['bank_id'])
                ->setCode($bank['bank_type'])
                ->setLabel($bank['label'])
                ->setBalance((float) $bank['balance'])
                ->setAccountName($bank['atas_nama'])
                ->setAccountNumber($bank['account_number'])
                ->setIsActive((bool) $bank['is_active']);
        }

        return $banks;
    }
}

<?php

namespace App\Services\Payments\Vendors;

use App\Http\Integrations\Moota\MootaConnector;
use App\Http\Integrations\Moota\Requests\BankAccount;
use App\Http\Integrations\Moota\Requests\ReceiveMoney;
use App\Services\Payments\DTO\PaymentUrl;
use App\Services\Payments\Payment;
use Illuminate\Support\Facades\Log;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

readonly class Moota implements Payment
{
    public function __construct(private MootaConnector $connector) {}

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
     */
    public function createPaymentUrl(PaymentUrl $paymentUrl): ?PaymentUrl
    {
        $response = $this->connector->send(new ReceiveMoney([
            'order_id' => $paymentUrl->getOrderId(),
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
            ->setOrderId($response->json('order_id'))
            ->setBankId($response->json('bank_id'))
            ->setPaymentUrl($response->json('payment_url'));
    }

    public function getRegisteredBanks(): array
    {
        $response = $this->connector->send(new BankAccount);
        if ($response->failed()) {
            Log::warning('Failed to get registered banks: '.$response->body());

            return [];
        }

        return [];
    }
}

<?php

namespace App\Services\Payments\Vendors;

use App\Http\Integrations\Moota\MootaConnector;
use App\Http\Integrations\Moota\Requests\ReceiveMoney;
use App\Services\Payments\DTO\PaymentLink;
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
    public function createPaymentLink(PaymentLink $paymentLink): array
    {
        $response = $this->connector->send(new ReceiveMoney([
            'order_id' => $paymentLink->getOrderId(),
            'bank_id' => $paymentLink->getBankId(),
            'customers' => $paymentLink->getCustomer(),
            'items' => $paymentLink->getItems(),
            'description' => $paymentLink->getDescription(),
            'note' => $paymentLink->getNote(),
            'redirect_url' => $paymentLink->getRedirectUrl(),
            'total' => $paymentLink->getTotal(),
        ]));

        if ($response->failed()) {
            Log::warning('Failed to create payment link: '.$response->body());

            return [];
        }

        return $response->json();
    }
}

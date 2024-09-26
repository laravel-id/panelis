<?php

namespace App\Actions\Transaction;

use App\Models\Transaction\Item;
use App\Models\Transaction\Transaction;
use App\Services\Payments\DTO\PaymentUrl;
use App\Services\Payments\Factory;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePaymentUrl
{
    use AsAction;

    public function __construct(private readonly Factory $provider) {}

    public function handle(Transaction $transaction, string $description = ''): PaymentUrl
    {
        $items = $transaction->items->map(function (Item $item): array {
            return [
                'name' => $item->name,
                'description' => '',
                'qty' => $item->quantity,
                'price' => $item->price,
            ];
        });

        $payload = (new PaymentUrl)
            ->setId($transaction->ulid)
            ->setBankId($transaction->bank->vendor_id)
            ->setCustomer(...$transaction->metadata['customer'])
            ->setItems($items->toArray())
            ->setDescription($description ?? '')
            ->setRedirectUrl($transaction->metadata['redirect_url'])
            ->setTotal($transaction->total);

        $payment = $this->provider->createPayment($payload);

        if (! empty($payment->getPaymentUrl())) {
            // replace origin amount with modified amount from moota
            $draftPayment = $this->provider->getPayment($payment->getId());
            $transaction->total = $draftPayment->getTotal();

            $metadata = $transaction->metadata;
            $metadata['payment_url'] = $payment->getPaymentUrl();
            $transaction->metadata = $metadata;
            $transaction->vendor = $payment->getVendor();
            $transaction->vendor_id = $payment->getId();
            $transaction->save();
        }

        return $payment;
    }
}

<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CancelPayment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly string $id) {}

    public function resolveEndpoint(): string
    {
        return '/mutation-tracking/cancel';
    }

    protected function defaultBody(): array
    {
        return [
            'trx_id' => $this->id,
        ];
    }
}

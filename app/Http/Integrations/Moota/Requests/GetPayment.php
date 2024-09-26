<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetPayment extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly string $id) {}

    public function resolveEndpoint(): string
    {
        return '/mutation-tracking/detail';
    }

    protected function defaultQuery(): array
    {
        return [
            'trx_id' => $this->id,
        ];
    }
}

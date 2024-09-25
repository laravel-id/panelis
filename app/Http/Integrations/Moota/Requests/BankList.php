<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class BankList extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/bank/available';
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => 1,
            'per_page' => 10,
        ];
    }
}

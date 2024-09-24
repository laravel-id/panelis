<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ReceiveMoney extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly array $data) {}

    public function resolveEndpoint(): string
    {
        return '/mutation-tracking';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}

<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GenerateToken extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly array $scopes = ['api'],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/auth/login';
    }

    protected function defaultBody(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'scopes' => $this->scopes,
        ];
    }
}

<?php

namespace App\Http\Integrations\Moota;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class MootaConnector extends Connector
{
    public function __construct(private readonly ?string $token = null) {}

    public function resolveBaseUrl(): string
    {
        return 'https://app.moota.co/api/v2';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator($this->token ?? '');
    }
}

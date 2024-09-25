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
        $path = '/api/v2';
        $host = rtrim(config('moota.host', ''), '/');
        if (empty($host)) {
            if (app()->isProduction()) {
                $host = 'https://app.moota.co';
            } else {
                $host = 'https://private-anon-17aa1ec034-mootaapiv2.apiary-mock.com';
            }
        }

        return $host.$path;
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

<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Request;

class RevokeToken extends Request
{
    protected Method $method = Method::POST;

    public function __construct(private readonly string $token) {}

    public function resolveEndpoint(): string
    {
        return '/auth/logout';
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator($this->token);
    }
}

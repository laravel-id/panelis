<?php

namespace App\Http\Integrations\Moota\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class UserProfile extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/user';
    }
}

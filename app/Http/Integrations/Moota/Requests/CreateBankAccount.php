<?php

namespace App\Http\Integrations\Moota\Requests;

use App\Services\Payments\DTO\Bank;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateBankAccount extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(private readonly Bank $bank) {}

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/bank/store';
    }

    protected function defaultBody(): array
    {
        return [
            'bank_type' => $this->bank->getCode(),
            'username' => $this->bank->getUsername(),
            'password' => $this->bank->getPassword(),
            'name_holder' => $this->bank->getAccountName(),
            'account_number' => $this->bank->getAccountNumber(),
            'is_active' => $this->bank->isActive(),
        ];
    }
}

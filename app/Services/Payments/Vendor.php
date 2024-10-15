<?php

namespace App\Services\Payments;

enum Vendor: string
{
    case Moota = 'moota';

    public function label(): string
    {
        return match ($this->value) {
            'moota' => 'Moota',
            default => __('Not Found'),
        };
    }
}

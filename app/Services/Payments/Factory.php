<?php

namespace App\Services\Payments;

use App\Http\Integrations\Moota\MootaConnector;
use App\Services\Payments\Vendors\Moota;
use Illuminate\Container\Container;
use Illuminate\Support\Manager;

/**
 * @mixin Payment
 */
class Factory extends Manager
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function getDefaultDriver(): string
    {
        return Vendor::Moota->value;
    }

    public function getDrivers(): array
    {
        return array_column(Vendor::cases(), 'value');
    }

    public function createMootaDriver(): Payment
    {
        return new Moota(new MootaConnector(config('moota.token')));
    }
}

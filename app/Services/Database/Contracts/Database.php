<?php

namespace App\Services\Database\Contracts;

use BackedEnum;

interface Database
{
    public function isAvailable(): bool;

    public function getDriver(): BackedEnum;

    public function getVersion(): ?string;

    public function getErrorMessage(): string;

    public function backup(): ?string;
}

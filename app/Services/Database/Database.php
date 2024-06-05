<?php

namespace App\Services\Database;

interface Database
{
    public function isAvailable(): bool;

    public function getVersion(): ?string;

    public function getErrorMessage(): string;

    public function backup(): void;
}

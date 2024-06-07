<?php

namespace App\Models\Enums;

interface HasOption
{
    public static function options(): array;

    public function label(): string;
}

<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Number;

enum NumberFormat: string implements HasLabel
{
    case Plain = 'plain';

    case PlainCommaDecimal = 'plain_comma_decimal';

    case DotWithoutDecimal = 'dot_without_decimal';

    case CommaWithoutDecimal = 'comma_without_decimal';

    case DotWithDecimal = 'dot_with_decimal';

    case CommaWithDecimal = 'comma_with_decimal';

    public function display(): array
    {
        return match ($this) {
            self::PlainCommaDecimal => [
                'decimals' => 2,
                'decimal_separator' => ',',
                'thousands_separator' => '',
            ],
            self::DotWithoutDecimal => [
                'decimals' => 0,
                'decimal_separator' => '',
                'thousands_separator' => ',',
            ],
            self::CommaWithoutDecimal => [
                'decimals' => 0,
                'decimal_separator' => '',
                'thousands_separator' => '.',
            ],
            self::DotWithDecimal => [
                'decimals' => 2,
                'decimal_separator' => '.',
                'thousands_separator' => ',',
            ],
            self::CommaWithDecimal => [
                'decimals' => 2,
                'decimal_separator' => ',',
                'thousands_separator' => '.',
            ],
            default => [
                'decimals' => 0,
                'decimal_separator' => '',
                'thousands_separator' => '',
            ],
        };
    }

    public function getLabel(): string
    {
        return Number::money(10_000.12, $this->value, '', '');
    }
}

<?php

namespace App\Models\Enums;

use Illuminate\Support\Number;

enum NumberFormat: string
{
    case Plain = '';

    case PlainCommaDecimal = '2 , ';

    case DotWithoutDecimal = '0 . ,';

    case CommaWithoutDecimal = '0 , .';

    case DotWithDecimal = '2 . ,';

    case CommaWithDecimal = '2 , .';

    public static function options(): array
    {
        return collect(NumberFormat::cases())
            ->mapWithKeys(function ($format) {
                return [$format->value => $format->getFormattedNumber()];
            })
            ->toArray();
    }

    public function getFormattedNumber(): string
    {
        return Number::money(10_000.12, format: $this->value, symbol: '');
    }
}

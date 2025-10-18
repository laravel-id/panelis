<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Number;

enum NumberFormat: string implements HasLabel
{
    case Plain = '';

    case PlainCommaDecimal = '2 , ';

    case DotWithoutDecimal = '0 . ,';

    case CommaWithoutDecimal = '0 , .';

    case DotWithDecimal = '2 . ,';

    case CommaWithDecimal = '2 , .';

    public function getLabel(): string
    {
        return Number::money(10_000.12, format: $this->value, symbol: '');
    }
}

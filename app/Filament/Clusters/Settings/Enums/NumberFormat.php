<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;
use Illuminate\Support\Number;

enum NumberFormat: string implements HasOption
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
            ->mapWithKeys(function (NumberFormat $format) {
                return [$format->value => $format->label()];
            })
            ->toArray();
    }

    public function label(): string
    {
        return Number::money(10_000.12, format: $this->value, symbol: '');
    }
}

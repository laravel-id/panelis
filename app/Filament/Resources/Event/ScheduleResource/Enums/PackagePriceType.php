<?php

namespace App\Filament\Resources\Event\ScheduleResource\Enums;

use App\Models\Enums\HasOption;

enum PackagePriceType: string implements HasOption
{
    case EarlyBird = 'early_bird';

    case LateBird = 'late_bird';

    case PreSale = 'pre_sale';

    case Normal = 'normal';

    case Group = 'group';

    case VIP = 'vip';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(function (self $case): array {
                return [$case->value => $case->label()];
            })
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('event.package_type_type_%s', $this->value));
    }
}

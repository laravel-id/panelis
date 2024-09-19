<?php

namespace App\Enums;

use App\Models\Enums\HasOption;

enum ThemeMode: string implements HasOption
{
    case System = 'system';

    case Light = 'light';

    case Dark = 'dark';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('setting.theme_%s', $this->value));
    }
}

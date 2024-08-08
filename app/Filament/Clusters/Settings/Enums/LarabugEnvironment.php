<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum LarabugEnvironment: string implements HasOption
{
    case Production = 'production';

    case Staging = 'staging';

    case Local = 'local';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(function (self $case) {
                return [$case->value => $case->label()];
            })
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('setting.environment_%s', $this->value));
    }
}

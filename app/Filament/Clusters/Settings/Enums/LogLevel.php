<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum LogLevel: string implements HasOption
{
    case Debug = 'debug';

    case Info = 'info';

    case Notice = 'notice';

    case Warning = 'warning';

    case Error = 'error';

    case Critical = 'critical';

    case Alert = 'alert';

    case Emergency = 'emergency';

    public static function options(): array
    {
        return collect(LogLevel::cases())
            ->mapWithKeys(function (LogLevel $case): array {
                return [$case->value => $case->label()];
            })
            ->toArray();
    }

    public function label(): string
    {
        return __(sprintf('setting.log.level_%s', $this->value));
    }
}

<?php

namespace App\Models\Enums;

enum LogLevel: string
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
                return [$case->value => $case->getLabel()];
            })
            ->toArray();
    }

    public function getLabel(): string
    {
        return __(sprintf('setting.log_level_%s', $this->value));
    }
}

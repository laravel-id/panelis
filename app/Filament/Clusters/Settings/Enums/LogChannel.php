<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum LogChannel: string implements HasOption
{
    case Single = 'single';

    case Daily = 'daily';

    case Slack = 'slack';

    case Syslog = 'syslog';

    case Errorlog = 'errorlog';

    case Monolog = 'monolog';

    case Papertrail = 'papertrail';

    public static function options(): array
    {
        return collect(LogChannel::cases())
            ->mapWithKeys(function (LogChannel $case): array {
                return [$case->value => $case->label()];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function descriptions(): array
    {
        return collect(LogChannel::cases())
            ->mapWithKeys(function (LogChannel $case): array {
                return [$case->value => $case->description()];
            })
            ->toArray();
    }

    public function label(): string
    {
        return __(sprintf('setting.log_type_%s', $this->value));
    }

    public function description(): string
    {
        return __(sprintf('setting.log_%s_description', $this->value));
    }
}

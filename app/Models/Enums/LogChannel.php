<?php

namespace App\Models\Enums;

enum LogChannel: string
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
                return [$case->value => $case->getLabel()];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function descriptions(): array
    {
        return collect(LogChannel::cases())
            ->mapWithKeys(function (LogChannel $case): array {
                return [$case->value => __(sprintf('setting.log_%s_description', $case->value))];
            })
            ->toArray();
    }

    public function getLabel(): string
    {
        return __(sprintf('setting.log_%s', $this->value));
    }
}

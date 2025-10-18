<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum LogChannel: string implements HasDescription, HasLabel
{
    case Single = 'single';

    case Daily = 'daily';

    case Slack = 'slack';

    case Syslog = 'syslog';

    case Errorlog = 'errorlog';

    case Monolog = 'monolog';

    case Nightwatch = 'nightwatch';

    case Papertrail = 'papertrail';

    public function getLabel(): string
    {
        return __(sprintf('setting.log.%s', $this->value));
    }

    public function getDescription(): string
    {
        return __(sprintf('setting.log.%s_description', $this->value));
    }
}

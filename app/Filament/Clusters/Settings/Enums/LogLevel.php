<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasLabel;

enum LogLevel: string implements HasLabel
{
    case Debug = 'debug';

    case Info = 'info';

    case Notice = 'notice';

    case Warning = 'warning';

    case Error = 'error';

    case Critical = 'critical';

    case Alert = 'alert';

    case Emergency = 'emergency';

    public function getLabel(): string
    {
        return __(sprintf('setting.log.level_%s', $this->value));
    }
}

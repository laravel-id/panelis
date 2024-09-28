<?php

namespace App\Enums;

use App\Models\Enums\HasOption;
use App\Models\Traits\HasOption as Option;

enum NotificationChannels: string implements HasOption
{
    use Option;

    case Email = 'mail';

    case Database = 'database';

    public function label(): string
    {
        return __(sprintf('ui.notification_channel_%s', $this->value));
    }
}

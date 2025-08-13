<?php

namespace App\Filament\Resources\Job\FailedJobResource\Enums;

enum FailedJobPermission: string
{
    case Browse = 'BrowseFailedJob';

    case Retry = 'RetryFailedJob';

    case Delete = 'DeleteFailedJob';
}

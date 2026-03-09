<?php

namespace Modules\Job\Panel\Resources\FailedJobResource\Enums;

enum FailedJobPermission: string
{
    case Browse = 'BrowseFailedJob';

    case Retry = 'RetryFailedJob';

    case Delete = 'DeleteFailedJob';
}

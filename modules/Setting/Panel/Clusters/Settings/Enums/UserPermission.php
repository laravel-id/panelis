<?php

namespace Modules\Setting\Panel\Clusters\Settings\Enums;

enum UserPermission: string
{
    case Browse = 'BrowseUserSetting';
    case Edit = 'EditUserSetting';
}

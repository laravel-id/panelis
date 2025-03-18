<?php

namespace App\Filament\Clusters\Settings\Enums;

enum UserPermission: string
{
    case Browse = 'BrowseUserSetting';
    case Edit = 'EditUserSetting';
}

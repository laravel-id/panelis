<?php

namespace App\Filament\Clusters\Settings\Enums;

enum CustomSettingPermission: string
{
    case Browse = 'BrowseCustomSetting';

    case Edit = 'EditCustomSetting';
}

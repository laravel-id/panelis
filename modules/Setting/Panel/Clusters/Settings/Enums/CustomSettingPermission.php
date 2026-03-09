<?php

namespace Modules\Setting\Panel\Clusters\Settings\Enums;

enum CustomSettingPermission: string
{
    case Browse = 'BrowseCustomSetting';

    case Edit = 'EditCustomSetting';
}

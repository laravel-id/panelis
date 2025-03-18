<?php

namespace App\Filament\Clusters\Settings\Enums;

enum SettingPermission: string
{
    case Browse = 'BrowseSetting';

    case Edit = 'EditSetting';

    case Export = 'ExportSetting';

    case Import = 'ImportSetting';
}

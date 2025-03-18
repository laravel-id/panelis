<?php

namespace App\Filament\Clusters\Settings\Enums;

enum LogPermission: string
{
    case Browse = 'BrowseLogSetting';

    case Edit = 'EditLogSetting';
}

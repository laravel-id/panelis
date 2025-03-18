<?php

namespace App\Filament\Clusters\Settings\Enums;

enum ThemePermission: string
{
    case Browse = 'BrowseThemeSetting';

    case Edit = 'EditThemeSetting';
}

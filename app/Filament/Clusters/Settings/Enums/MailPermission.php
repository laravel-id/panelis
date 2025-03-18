<?php

namespace App\Filament\Clusters\Settings\Enums;

enum MailPermission: string
{
    case Browse = 'BrowseMailSetting';

    case Edit = 'EditMailSetting';

    case SendTest = 'SendTestMailSetting';
}

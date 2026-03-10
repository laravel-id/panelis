<?php

namespace Modules\Setting\Panel\Clusters\Settings\Enums;

enum MailPermission: string
{
    case Browse = 'BrowseMailSetting';

    case Edit = 'EditMailSetting';

    case SendTest = 'SendTestMailSetting';
}

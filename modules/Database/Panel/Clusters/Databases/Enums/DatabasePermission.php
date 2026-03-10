<?php

namespace Modules\Database\Panel\Clusters\Databases\Enums;

enum DatabasePermission: string
{
    case Browse = 'BrowseDatabase';

    case Read = 'ReadDatabase';

    case Edit = 'EditDatabase';

    case Add = 'AddDatabase';

    case Delete = 'DeleteDatabase';

    case Backup = 'BackupDatabase';

    case Download = 'DownloadDatabase';
}

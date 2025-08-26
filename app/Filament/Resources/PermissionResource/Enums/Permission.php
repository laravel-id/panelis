<?php

namespace App\Filament\Resources\PermissionResource\Enums;

enum Permission: string
{
    case Browse = 'BrowseUserPermission';

    case Read = 'ReadUserPermission';

    case Edit = 'EditUserPermission';

    case Create = 'CreateUserPermission';

    case Delete = 'DeleteUserPermission';
    case Backup = 'BackupUserPermission';
}

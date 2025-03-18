<?php

namespace App\Filament\Resources\PermissionResource\Enums;

enum Permission: string
{
    case Browse = 'BrowsePermissionUser';

    case Read = 'ReadPermissionUser';

    case Edit = 'EditPermissionUser';

    case Add = 'AddPermissionUser';

    case Delete = 'DeletePermissionUser';
    case Backup = 'BackupPermissionUser';
}

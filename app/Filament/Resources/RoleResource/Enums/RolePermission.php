<?php

namespace App\Filament\Resources\RoleResource\Enums;

enum RolePermission: string
{
    case Browse = 'BrowseRoleUser';

    case Read = 'ReadRoleUser';

    case Edit = 'EditRoleUser';

    case Add = 'AddRoleUser';

    case Delete = 'DeleteRoleUser';
}

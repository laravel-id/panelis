<?php

namespace Modules\User\Panel\Resources\RoleResource\Enums;

enum RolePermission: string
{
    case Browse = 'BrowseUserRole';

    case Read = 'ReadUserRole';

    case Edit = 'EditUserRole';

    case Create = 'CreateUserRole';

    case Delete = 'DeleteUserRole';
}

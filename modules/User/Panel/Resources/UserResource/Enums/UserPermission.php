<?php

namespace Modules\User\Panel\Resources\UserResource\Enums;

enum UserPermission: string
{
    case Browse = 'BrowseUser';

    case Read = 'ReadUser';

    case Edit = 'EditUser';

    case Create = 'CreateUser';

    case Delete = 'DeleteUser';

    case ResetPassword = 'ResetPasswordUser';
}

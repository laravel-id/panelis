<?php

namespace App\Filament\Resources\UserResource\Enums;

enum UserPermission: string
{
    case Browse = 'BrowseUser';

    case Read = 'ReadUser';

    case Edit = 'EditUser';

    case Create = 'CreateUser';

    case Delete = 'DeleteUser';

    case ResetPassword = 'ResetPasswordUser';
}

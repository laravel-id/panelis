<?php

namespace App\Filament\Resources\UserResource\Enums;

enum UserPermission: string
{
    case Browse = 'BrowseUser';

    case Read = 'ReadUser';

    case Edit = 'EditUser';

    case Add = 'AddUser';

    case Delete = 'DeleteUser';

    case ResetPassword = 'ResetPasswordUser';
}

<?php

namespace App\Filament\Resources\ModuleResource\Enums;

enum ModulePermission: string
{
    case Browse = 'BrowseModule';

    case Read = 'ReadModule';

    case Edit = 'EditModule';

    case Add = 'AddModule';

    case Delete = 'DeleteModule';
}

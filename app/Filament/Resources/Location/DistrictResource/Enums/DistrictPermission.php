<?php

namespace App\Filament\Resources\Location\DistrictResource\Enums;

enum DistrictPermission: string
{
    case Browse = 'BrowseDistrictLocation';

    case Read = 'ReadDistrictLocation';

    case Edit = 'EditDistrictLocation';

    case Add = 'AddDistrictLocation';

    case Delete = 'DeleteDistrictLocation';
}

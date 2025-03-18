<?php

namespace App\Filament\Resources\Location\RegionResource\Enums;

enum RegionPermission: string
{
    case Browse = 'BrowseRegionLocation';

    case Read = 'ReadRegionLocation';

    case Edit = 'EditRegionLocation';

    case Add = 'AddRegionLocation';

    case Delete = 'DeleteRegionLocation';
}

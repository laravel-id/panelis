<?php

namespace Modules\Location\Panel\Resources\DistrictResource\Enums;

enum DistrictPermission: string
{
    case Browse = 'BrowseDistrictLocation';

    case Read = 'ReadDistrictLocation';

    case Edit = 'EditDistrictLocation';

    case Add = 'AddDistrictLocation';

    case Delete = 'DeleteDistrictLocation';
}

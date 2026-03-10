<?php

namespace Modules\Location\Panel\Resources\CountryResource\Enums;

enum CountryPermission: string
{
    case Browse = 'BrowseCountryLocation';

    case Read = 'ReadCountryLocation';

    case Edit = 'EditCountryLocation';

    case Add = 'AddCountryLocation';

    case Delete = 'DeleteCountryLocation';
}

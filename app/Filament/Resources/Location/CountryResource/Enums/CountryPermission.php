<?php

namespace App\Filament\Resources\Location\CountryResource\Enums;

enum CountryPermission: string
{
    case Browse = 'BrowseCountryLocation';

    case Read = 'ReadCountryLocation';

    case Edit = 'EditCountryLocation';

    case Add = 'AddCountryLocation';

    case Delete = 'DeleteCountryLocation';
}

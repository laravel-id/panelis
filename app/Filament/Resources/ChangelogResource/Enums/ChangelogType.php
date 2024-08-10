<?php

namespace App\Filament\Resources\ChangelogResource\Enums;

enum ChangelogType: string
{
    case Feature = 'feature';

    case Bug = 'bug';

    case Style = 'style';

    case Refactor = 'refactor';

    case Test = 'test';

    case Revert = 'revert';
}

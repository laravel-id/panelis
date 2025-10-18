<?php

namespace App\Filament\Clusters\Databases\Enums;

use Filament\Support\Contracts\HasLabel;

enum CloudProvider: string implements HasLabel
{
    case Dropbox = 'dropbox';

    //    case GoogleDrive = 'google_drive';

    public function getLabel(): string
    {
        return __(sprintf('database.cloud_storage_%s', $this->value));
    }
}

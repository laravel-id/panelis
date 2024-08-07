<?php

namespace App\Filament\Clusters\Databases\Enums;

use App\Models\Enums\HasOption;

enum CloudProvider: string implements HasOption
{
    case Dropbox = 'dropbox';

    //    case GoogleDrive = 'google_drive';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(function (self $case): array {
                return [$case->value => $case->label()];
            })
            ->all();
    }

    public function label(): string
    {
        return __(sprintf('database.cloud_storage_%s', $this->value));
    }
}

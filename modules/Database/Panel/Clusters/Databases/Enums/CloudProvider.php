<?php

namespace Modules\Database\Panel\Clusters\Databases\Enums;

use Composer\InstalledVersions;
use Filament\Support\Contracts\HasLabel;

enum CloudProvider: string implements HasLabel
{
    case Dropbox = 'dropbox';

    //    case GoogleDrive = 'google_drive';

    public function getLabel(): string
    {
        return __(sprintf('database::database.cloud_storage_%s', $this->value));
    }

    public function getScopes(): array
    {
        return match ($this) {
            self::Dropbox => ['files.content.write'],
        };
    }

    public function isInstalled(): bool
    {
        return match ($this) {
            self::Dropbox => InstalledVersions::isInstalled('spatie/flysystem-dropbox') && InstalledVersions::isInstalled('socialiteproviders/dropbox'),
            default => false,
        };
    }
}

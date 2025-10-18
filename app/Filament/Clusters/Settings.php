<?php

namespace App\Filament\Clusters;

use App\Filament\Clusters\Settings\Enums\SettingPermission;
use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('ui.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.label');
    }

    public static function canAccess(): bool
    {
        return user_can(SettingPermission::Browse);
    }
}

<?php

namespace App\Filament\Clusters;

use App\Filament\Clusters\Settings\Enums\SettingPermission;
use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting');
    }

    public static function canAccess(): bool
    {
        return user_can(SettingPermission::Browse);
    }
}

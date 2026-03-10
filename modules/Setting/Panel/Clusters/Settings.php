<?php

namespace Modules\Setting\Panel\Clusters;

use Filament\Clusters\Cluster;
use Modules\Setting\Panel\Clusters\Settings\Enums\SettingPermission;

class Settings extends Cluster
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('ui.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.label');
    }

    public static function canAccess(): bool
    {
        return user_can(SettingPermission::Browse);
    }
}

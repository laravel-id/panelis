<?php

namespace App\Filament\Clusters;

use App\Filament\Clusters\Databases\Enums\DatabasePermission;
use Filament\Clusters\Cluster;

class Databases extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.database');
    }

    public static function canAccess(): bool
    {
        return user_can(DatabasePermission::Browse);
    }
}

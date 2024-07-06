<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

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
        return Auth::user()->can('ViewSetting');
    }
}

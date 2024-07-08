<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

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
        return Auth::user()->can('ViewDb');
    }
}

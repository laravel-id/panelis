<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Databases extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.system');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.database');
    }
}

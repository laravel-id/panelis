<?php

namespace App\Services\Database;

use App\Filament\Clusters\Databases\Pages\DatabaseType;
use App\Services\Database\Vendors\MySQL;
use App\Services\Database\Vendors\SQLite;

class DatabaseFactory
{
    public static function make(): ?object
    {
        return match (config('database.default')) {
            DatabaseType::SQLite->value => new SQLite,
            DatabaseType::MySQL->value => new MySQL,
            default => null,
        };
    }
}

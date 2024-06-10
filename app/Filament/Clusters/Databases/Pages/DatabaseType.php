<?php

namespace App\Filament\Clusters\Databases\Pages;

enum DatabaseType: string
{
    case SQLite = 'sqlite';

    case MySQL = 'mysql';

    case PostgreSQL = 'postgresql';

    public static function getType(string $type): string
    {
        return match ($type) {
            DatabaseType::SQLite->value => 'SQLite',
            DatabaseType::MySQL->value => 'MySQL',
            DatabaseType::PostgreSQL->value => 'PostgreSQL',
        };
    }
}

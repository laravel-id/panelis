<?php

namespace Modules\Database\Services\Database\Enums;

enum DatabaseDriver: string
{
    case MySQL = 'mysql';

    case PostgreSQL = 'pgsql';

    case SQLite = 'sqlite';

    public static function isSupported(string $driver): bool
    {
        return in_array($driver, array_column(self::cases(), 'value'), true);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MySQL => 'MySQL',
            self::PostgreSQL => 'PostgreSQL',
            self::SQLite => 'SQLite',
            default => null,
        };
    }
}

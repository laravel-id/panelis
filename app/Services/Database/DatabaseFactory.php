<?php

namespace App\Services\Database;

use App\Services\Database\Vendors\SQLite;

class DatabaseFactory
{
    public static function make()
    {
        if (config('database.default') === 'sqlite') {
            return new SQLite;
        }
    }
}

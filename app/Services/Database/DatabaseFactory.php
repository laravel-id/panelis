<?php

namespace App\Services\Database;

use App\Services\Database\Vendors\MySQL;
use App\Services\Database\Vendors\SQLite;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

/**
 * @mixin Database
 */
class DatabaseFactory extends Manager
{
    private const SQLite = 'sqlite';

    private const MySQL = 'mysql';

    protected $drivers = [
        self::SQLite,
        self::MySQL,
    ];

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function getDefaultDriver(): string
    {
        return self::SQLite;
    }

    protected function createSqliteDriver(): Database
    {
        return new SQLite;
    }

    protected function createMysqlDriver(): Database
    {
        return new MySQL;
    }
}

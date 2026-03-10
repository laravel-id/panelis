<?php

namespace Modules\Database\Services\Database;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use Modules\Database\Services\Database\Contracts\Database as Contract;
use Modules\Database\Services\Database\Enums\DatabaseDriver;
use Modules\Database\Services\Database\Vendors\MySQL;
use Modules\Database\Services\Database\Vendors\PostgreSQL;
use Modules\Database\Services\Database\Vendors\SQLite;

/**
 * @mixin Database
 */
class Database extends Manager
{
    protected $drivers = [
        DatabaseDriver::MySQL->value,
        DatabaseDriver::PostgreSQL->value,
        DatabaseDriver::SQLite->value,
    ];

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function getDefaultDriver(): string
    {
        return DatabaseDriver::SQLite->value;
    }

    protected function createSqliteDriver(): Contract
    {
        return $this->container->make(SQLite::class);
    }

    protected function createMysqlDriver(): Contract
    {
        return $this->container->make(MySQL::class);
    }

    protected function createPgsqlDriver(): Contract
    {
        return $this->container->make(PostgreSQL::class);
    }
}

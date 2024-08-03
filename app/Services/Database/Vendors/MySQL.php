<?php

namespace App\Services\Database\Vendors;

use App\Services\Database\Database;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class MySQL implements Database
{
    private ?string $errorMessage;

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function isAvailable(): bool
    {
        $command = Process::run('mysqldump --version');

        if (! $command->successful()) {
            $this->errorMessage = $command->errorOutput();
        }

        return $command->successful();
    }

    public function getVersion(): ?string
    {
        $command = Process::run('mysqldump --version');

        return $command->output();
    }

    public function backup(): ?string
    {
        $db = config('database.connections.mysql');
        $command = vsprintf('mysqldump --skip-comments -h%s -P%s -u%s -p%s %s > %s', [
            $db['host'],
            $db['port'],
            $db['username'],
            $db['password'],
            $db['database'],
            $destination = sprintf('%s.sql', time()),
        ]);

        $output = Process::path(storage_path('app/database'))
            ->run($command)
            ->throw();
        if (! $output->successful()) {
            $this->errorMessage = $output->errorOutput();
            Log::error($this->errorMessage);

            return null;
        }

        return $destination;
    }
}

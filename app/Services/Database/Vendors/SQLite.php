<?php

namespace App\Services\Database\Vendors;

use App\Services\Database\Database;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class SQLite implements Database
{
    private string $errorMessage = '';

    public function isAvailable(): bool
    {
        $command = Process::run('sqlite3 -version');

        if (! $command->successful()) {
            $this->errorMessage = $command->errorOutput();
        }

        return $command->successful();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getVersion(): ?string
    {
        $command = Process::run('sqlite3 -version');

        return $command->output() ?? '';
    }

    public function backup(): void
    {
        $database = config('database.connections.sqlite.database');
        $filename = sprintf('%s.sqlite', time());

        if (! Storage::disk('local')->directoryExists('database')) {
            Storage::makeDirectory('database');
        }
        $destination = sprintf('%s/%s', storage_path('app/database'), $filename);

        Process::path(database_path())
            ->run(sprintf('sqlite3 %s ".backup %s"', basename($database), $destination));
    }
}

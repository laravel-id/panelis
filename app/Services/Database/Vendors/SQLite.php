<?php

namespace App\Services\Database\Vendors;

use App\Services\Database\Database;
use Illuminate\Support\Facades\Log;
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

        $versions = explode(' ', $command->output());
        if (count($versions) >= 2) {
            return sprintf('%s %s', $versions[0], $versions[1]);
        }

        return null;

    }

    public function backup(): void
    {
        $database = config('database.connections.sqlite.database');
        $filename = sprintf('%s.sql', time());

        if (! Storage::disk('local')->directoryExists('database')) {
            Storage::makeDirectory('database');
        }
        $destination = sprintf('%s/%s', storage_path('app/database'), $filename);

        $command = Process::path(database_path())
            ->run(sprintf('sqlite3 %s .dump > %s', $database, $destination));

        if (!$command->successful()) {
            Log::error(__('Failed to run SQLite backup command.'), [
                'message' => $command->errorOutput(),
            ]);
        }
    }
}

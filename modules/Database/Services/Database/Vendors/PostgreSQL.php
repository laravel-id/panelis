<?php

namespace Modules\Database\Services\Database\Vendors;

use App\Enums\Disk;
use BackedEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Modules\Database\Services\Database\Contracts\Database;
use Modules\Database\Services\Database\Enums\DatabaseDriver;
use Throwable;

class PostgreSQL implements Database
{
    private ?string $errorMessage = null;

    public function getDriver(): BackedEnum
    {
        return DatabaseDriver::PostgreSQL;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
    }

    public function isAvailable(): bool
    {
        $command = Process::run('pg_dump --version');

        if (! $command->successful()) {
            $this->errorMessage = $command->errorOutput();
        }

        return $command->successful();
    }

    public function getVersion(): ?string
    {
        $command = Process::run('pg_dump --version');

        return $command->successful()
            ? trim($command->output())
            : null;
    }

    public function backup(): ?string
    {
        $db = config('database.connections.pgsql');

        $disk = Storage::disk(Disk::Local);

        $directory = 'database';
        $disk->makeDirectory($directory);

        $filename = sprintf('%s.sql', Carbon::now()->timestamp);

        $path = "{$directory}/{$filename}";

        $absolutePath = $disk->path($path);

        try {
            $output = Process::path(base_path())
                ->env([
                    'PGPASSWORD' => $db['password'],
                ])
                ->run(sprintf(
                    'pg_dump --no-owner --no-privileges -h%s -p%s -U%s -d%s > %s',
                    $db['host'],
                    $db['port'],
                    $db['username'],
                    $db['database'],
                    $absolutePath
                ));

            if (! $output->successful()) {
                $this->errorMessage = $output->errorOutput();

                Log::error($this->errorMessage);

                return null;
            }
        } catch (Throwable $e) {
            $this->errorMessage = $e->getMessage();

            Log::error($this->errorMessage);

            return null;
        }

        return $absolutePath;
    }
}

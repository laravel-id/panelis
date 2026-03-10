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

class MySQL implements Database
{
    private ?string $errorMessage = null;

    public function getDriver(): BackedEnum
    {
        return DatabaseDriver::MySQL;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
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

        return $command->successful()
            ? trim($command->output())
            : null;
    }

    public function backup(): ?string
    {
        $db = config('database.connections.mysql');

        $disk = Storage::disk(Disk::Local);

        $directory = 'database';
        $disk->makeDirectory($directory);

        $filename = sprintf('%s.sql', Carbon::now()->timestamp);

        $path = "{$directory}/{$filename}";
        $absolutePath = $disk->path($path);

        try {
            $output = Process::path(base_path())
                ->run(sprintf(
                    'mysqldump --skip-comments -h%s -P%s -u%s -p%s %s > %s',
                    $db['host'],
                    $db['port'],
                    $db['username'],
                    $db['password'],
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

        return $path;
    }
}

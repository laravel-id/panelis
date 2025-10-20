<?php

namespace App\Console\Commands\Database;

use App\Jobs\Database\UploadToCloud;
use App\Models\User;
use App\Services\Database\DatabaseFactory;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupCommand extends Command
{
    protected $signature = 'app:backup-database';

    protected $description = 'Backup database based on scheduled time';

    public function handle(DatabaseFactory $database): int
    {
        if (! $database->isAvailable()) {
            $this->error(__('database.auto_backup.not_available'));

            return self::FAILURE;
        }

        $users = User::query()
            ->doesntHave('roles')
            ->get();

        try {
            $path = $database->backup();

            if (! file_exists($path)) {
                throw new Exception('Backup file not found at '.$path);
            }

            if (config('database.cloud_backup_enabled')) {
                UploadToCloud::dispatch($path);
            }

            $this->cleanupOldBackups();

            Notification::make()
                ->title(__('database.file_created'))
                ->success()
                ->sendToDatabase($users);

            $this->info(__('database.auto_backup.backed_up', ['path' => $path]));

            return self::SUCCESS;
        } catch (Exception $e) {
            Log::error($e);

            Notification::make()
                ->title(__('database.file_not_created'))
                ->warning()
                ->sendToDatabase($users);

            return self::FAILURE;
        }
    }

    protected function cleanupOldBackups(): void
    {
        $storage = Storage::disk('local');
        $files = collect($storage->allFiles('database'))
            ->filter(fn ($f) => str_ends_with($f, '.sql') || str_ends_with($f, '.zip'))
            ->sortBy(fn ($f) => $storage->lastModified($f))
            ->values();

        $max = (int) config('database.backup_max', 5);

        if ($files->count() <= $max) {
            return;
        }

        $toDelete = $files->take($files->count() - $max);

        $toDelete->each(function ($file) use ($storage) {
            $storage->delete($file);
        });
    }
}

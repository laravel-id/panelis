<?php

namespace App\Console\Commands\Database;

use App\Jobs\Database\UploadToCloud;
use App\Models\Database as DB;
use App\Models\User;
use App\Services\Database\DatabaseFactory;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database based on scheduled time';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseFactory $database): int
    {
        if (! $database->isAvailable()) {
            $this->error(__('Database backup is not available.'));

            return Command::FAILURE;
        }

        $users = User::query()
            ->doesntHave('roles')
            ->get();

        try {
            $path = $database->backup();
            if (file_exists($path)) {
                // upload backed up file to the cloud
                if (config('database.cloud_backup_enabled')) {
                    UploadToCloud::dispatch($path);
                }

                // remove old backup, only on local storage
                if (DB::count() > (int) config('database.backup_max')) {
                    $db = DB::query()
                        ->orderBy('created_at')
                        ->first();

                    $storage = Storage::disk('local');
                    if ($storage->exists($db->path)) {
                        $storage->delete($db->path);
                    }
                }
            }

            Notification::make()
                ->title(__('database.file_created'))
                ->sendToDatabase($users);

            $this->info(__('Database has been backed up to :path.', ['path' => $path]));

            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error($e);
        }

        Notification::make()
            ->title(__('database.file_not_created'))
            ->sendToDatabase($users);

        return Command::FAILURE;
    }
}

<?php

namespace App\Console\Commands\Database;

use App\Models\Database as DB;
use App\Services\Database\Database;
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
    public function handle(Database $database): int
    {
        try {
            $database->backup();

            if (DB::count() > (int) config('database.backup_max')) {
                $db = DB::query()
                    ->orderBy('created_at')
                    ->first();

                $storage = Storage::disk('local');
                if ($storage->exists($db->path)) {
                    $storage->delete($db->path);
                }
            }

            return 0;
        } catch (\Exception $e) {
            Log::error($e);
        }

        return 1;
    }
}

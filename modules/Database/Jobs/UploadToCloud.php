<?php

namespace Modules\Database\Jobs;

use App\Enums\Disk;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadToCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $driver = config('database.cloud_storage');
        if (empty($driver)) {
            return;
        }

        [$time, $ext] = explode('.', basename($this->path), 2);
        $name = Carbon::createFromTimestamp($time)
            ->timezone(get_timezone())
            ->format('Y-m-d_H-i');
        $name = sprintf('%s-%s.%s', app()->environment(), $name, $ext);

        $db = Storage::disk(Disk::Local)->get($this->path);

        Storage::disk(config('database.cloud_storage'))->put($name, $db);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}

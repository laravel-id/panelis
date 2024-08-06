<?php

namespace App\Jobs\Database;

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
        [$time, $ext] = explode('.', basename($this->path), 2);
        $name = Carbon::createFromTimestamp($time)
            ->timezone(get_timezone())
            ->format('Y-m-d_H-i');
        $name = sprintf('%s-%s.%s', app()->environment(), $name, $ext);

        Storage::disk(config('database.cloud_storage'))->put($name, file_get_contents($this->path));
    }
}

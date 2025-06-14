<?php

use App\Jobs\Database\UploadToCloud;
use Illuminate\Support\Facades\Queue;

test('schedule database backup', function () {
    config()->set('database.cloud_backup_enabled', '1');
    Queue::fake([
        UploadToCloud::class,
    ]);

    $this->artisan('app:backup-database')
        ->assertExitCode(0);
});

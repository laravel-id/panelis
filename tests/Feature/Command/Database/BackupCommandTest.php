<?php

use App\Jobs\Database\UploadToCloud;
use App\Services\Database\DatabaseFactory;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('it runs backup command and uploads to cloud', function () {
    Bus::fake();
    config()->set('database.cloud_backup_enabled', true);
    config()->set('database.backup_max', 2);

    $dir = storage_path('app/database');
    File::ensureDirectoryExists($dir);

    $fakePath = $dir.'/test.sql';
    file_put_contents($fakePath, 'dummy content');

    $mockDatabase = Mockery::mock(DatabaseFactory::class);
    $mockDatabase->shouldReceive('isAvailable')->andReturnTrue();
    $mockDatabase->shouldReceive('backup')->andReturn($fakePath);

    $this->instance(DatabaseFactory::class, $mockDatabase);

    Storage::disk('local')->put('database/test-backup.sql', 'dummy-content');

    $this->artisan('app:backup-database', [
        '--no-interaction' => true,
    ])->assertExitCode(0);

    Bus::assertDispatched(UploadToCloud::class, function (UploadToCloud $job) use ($fakePath) {
        return $job->getPath() === $fakePath;
    });

    Storage::disk('local')->assertExists('database/test-backup.sql');
});

test('it handles unavailable database factory gracefully', function () {
    $mockDatabase = Mockery::mock(DatabaseFactory::class);
    $mockDatabase->shouldReceive('isAvailable')
        ->once()
        ->andReturnFalse();

    $this->instance(DatabaseFactory::class, $mockDatabase);

    $artisan = $this->artisan('app:backup-database', [
        '--no-interaction' => true,
    ]);

    $artisan->assertExitCode(1);
});

afterEach(function () {
    Mockery::close();
});

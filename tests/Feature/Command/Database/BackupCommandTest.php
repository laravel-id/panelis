<?php

namespace Tests\Feature\Command\Database;

use App\Services\Database\Database;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Modules\Database\Jobs\UploadToCloud;

test('it runs backup command and uploads to cloud', function () {
    Bus::fake();
    config()->set('database.cloud_backup_enabled', true);
    config()->set('database.backup_max', 2);

    $dir = storage_path('app/database');
    File::ensureDirectoryExists($dir);

    $fakePath = $dir.'/test.sql';
    file_put_contents($fakePath, 'dummy content');

    $mockDatabase = Mockery::mock(Database::class);
    $mockDatabase->shouldReceive('isAvailable')->andReturnTrue();
    $mockDatabase->shouldReceive('backup')->andReturn($fakePath);

    $this->instance(Database::class, $mockDatabase);

    Storage::disk('local')->put('database/test-backup.sql', 'dummy-content');

    $this->artisan('panelis:backup-database', [
        '--no-interaction' => true,
    ])->assertExitCode(0);

    Bus::assertDispatched(UploadToCloud::class, function (UploadToCloud $job) use ($fakePath) {
        return $job->getPath() === $fakePath;
    });

    Storage::disk('local')->assertExists('database/test-backup.sql');
});

test('it handles unavailable database factory gracefully', function () {
    $mockDatabase = Mockery::mock(Database::class);
    $mockDatabase->shouldReceive('isAvailable')
        ->once()
        ->andReturnFalse();

    $this->instance(Database::class, $mockDatabase);

    $artisan = $this->artisan('panelis:backup-database', [
        '--no-interaction' => true,
    ]);

    $artisan->assertExitCode(1);
});

afterEach(function () {
    Mockery::close();
});

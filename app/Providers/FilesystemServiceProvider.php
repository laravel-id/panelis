<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Modules\Database\Panel\Clusters\Databases\Enums\CloudProvider;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend(CloudProvider::Dropbox->value, function (Application $app, array $config): FilesystemAdapter {
            $adapter = new DropboxAdapter(new Client(config('filesystems.disks.dropbox.token')));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });
    }
}

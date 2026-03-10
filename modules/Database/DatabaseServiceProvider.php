<?php

namespace Modules\Database;

use Composer\InstalledVersions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Modules\Database\Commands\BackupCommand;
use Modules\Database\Panel\Clusters\Databases\Enums\CloudProvider;
use Modules\Database\Services\Database\Contracts\Database;
use Modules\Database\Services\Database\Database as DatabaseManager;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'database');

        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupCommand::class,
            ]);
        }

        if (InstalledVersions::isInstalled('socialiteproviders/dropbox')) {
            Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
                $event->extendSocialite('dropbox', \SocialiteProviders\Dropbox\Provider::class);
            });
        }

        if (InstalledVersions::isInstalled('spatie/flysystem-dropbox')) {
            Storage::extend(CloudProvider::Dropbox->value, function (Application $app, array $config): FilesystemAdapter {
                $adapter = new DropboxAdapter(new Client(config('filesystems.disks.dropbox.token')));

                return new FilesystemAdapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config,
                );
            });
        }

        Route::middleware(['auth', 'web'])
            ->prefix('panelis.database')
            ->name('panelis.database.')
            ->group(__DIR__.'/routes.php');
    }

    public function register(): void
    {
        $this->app->singleton(Database::class, function (Application $app): Database {
            return $app->make(DatabaseManager::class)->driver(config('database.default'));
        });
    }
}

<?php

namespace App\Jobs\Database;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Databases\Enums\CloudProvider;
use App\Models\Setting;
use App\Services\OAuth\OAuth;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
        $oauth = app(OAuth::class)
            ->driver(config('database.cloud_storage', CloudProvider::Dropbox->value));

        // get token by refresh token
        $refreshToken = config('dropbox.refresh_token');
        if (empty($refreshToken)) {
            Log::warning('Refresh token is not available.');

            return;
        }

        $auth = $oauth->setAppKey(config('dropbox.client_id'))
            ->setAppSecret(config('dropbox.client_secret'))
            ->authorize($refreshToken);
        if (! empty($auth->getError())) {
            Log::warning($auth->getError());

            return;
        }

        Setting::set('filesystems.disks.dropbox.token', $auth->getToken());
        event(new SettingUpdated);

        Config::set('filesystems.disks.dropbox.token', $auth->getToken());

        [$time, $ext] = explode('.', basename($this->path), 2);
        $name = Carbon::createFromTimestamp($time)
            ->timezone(get_timezone())
            ->format('Y-m-d_H-i');
        $name = sprintf('%s-%s.%s', app()->environment(), $name, $ext);

        Storage::disk(config('database.cloud_storage'))->put($name, file_get_contents($this->path));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}

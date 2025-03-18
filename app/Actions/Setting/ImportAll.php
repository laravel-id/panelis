<?php

namespace App\Actions\Setting;

use App\Events\SettingUpdated;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportAll
{
    use AsAction;

    /**
     * @throws Exception
     */
    public function handle(TemporaryUploadedFile $file): void
    {
        $contents = $file->getContent();
        if (! Str::isJson($contents)) {
            throw new Exception('Invalid file contents.');
        }

        $contents = json_decode($contents, true);

        DB::transaction(function () use ($contents): void {
            foreach ($contents as $setting) {
                if (! array_key_exists('key', $setting) || ! array_key_exists('value', $setting)) {
                    throw new Exception('Invalid setting format.');
                }

                Setting::set($setting['key'], $setting['value']);
            }
        });

        event(new SettingUpdated);
    }
}

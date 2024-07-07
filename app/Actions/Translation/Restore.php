<?php

namespace App\Actions\Translation;

use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class Restore
{
    use AsAction;

    private static string $disk = 'local';

    public function handle(): void
    {
        $files = Storage::disk(self::$disk)->allFiles('locales');
        foreach ($files as $file) {
            [$locale, $ext] = explode('.', basename($file), 2);
            unset($ext);

            $content = Storage::disk(self::$disk)->get($file);
            Import::run(json_decode($content, associative: true), $locale);
        }
    }
}

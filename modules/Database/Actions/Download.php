<?php

namespace Modules\Database\Actions;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class Download
{
    use AsAction;

    public function handle(FilesystemAdapter $storage, string $path, string $name): StreamedResponse
    {
        try {
            $fileName = vsprintf('%s-%s.%s', [
                Carbon::createFromTimestamp(Str::before($name, '.'))->format('Y-m-d_H-i-s'),
                config('database.default'),
                Str::after($name, '.'),
            ]);
        } catch (Throwable $e) {
            throw $e;
        }

        return response()->streamDownload(function () use ($storage, $path) {
            $stream = $storage->readStream($path);
            while (! feof($stream)) {
                echo fread($stream, 8192);
            }
            fclose($stream);
        }, $fileName);
    }
}

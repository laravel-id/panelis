<?php

namespace App\Http\Controllers\Panelis;

use App\Enums\Disk;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DatabaseController extends Controller
{
    public function download(string $file): StreamedResponse
    {
        abort_if(str_contains($file, '..'), 404);

        try {
            $fileName = vsprintf('%s.%s', [
                Carbon::createFromTimestamp(Str::before($file, '.'))->format('Y-m-d_H-i-s'),
                Str::after($file, '.'),
            ]);
        } catch (Throwable $e) {
            Log::error($e);

            abort(404);
        }

        $file = sprintf('database/%s', $file);
        $storage = Storage::disk(Disk::Local);

        abort_unless($storage->exists($file), 404);

        return response()->streamDownload(function () use ($file, $storage) {
            $stream = $storage->readStream($file);

            while (! feof($stream)) {
                echo fread($stream, 1024 * 8);
                flush();
            }

            fclose($stream);
        }, $fileName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}

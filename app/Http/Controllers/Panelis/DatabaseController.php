<?php

namespace App\Http\Controllers\Panelis;

use App\Enums\Disk;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Database\Actions\Download;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseController extends Controller
{
    public function download(string $file): StreamedResponse
    {
        abort_if(str_contains($file, '..'), 404);

        $file = sprintf('database/%s', $file);
        $storage = Storage::disk(Disk::Local);

        abort_unless($storage->exists($file), 404);

        return Download::run($storage, $file, basename($file));
    }
}

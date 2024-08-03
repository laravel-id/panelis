<?php

namespace App\Http\Controllers;

use App\Services\Database\Database;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Throwable;

class DatabaseController extends Controller
{
    public function backup(Request $request, Database $db): Response
    {
        if (! $request->user()->can('BackupDb')) {
            return response(__('Forbidden.'), Response::HTTP_FORBIDDEN);
        }

        try {
            $path = $db->backup();

            dispatch(function () use ($path): void {
                [$time, $ext] = explode('.', basename($path));
                $name = Carbon::createFromTimestamp($time)
                    ->timezone(get_timezone())
                    ->format('Y-m-d_H-i');
                $name = sprintf('%s-%s.%s', app()->environment(), $name, $ext);

                if (! Storage::disk('dropbox')->put($name, file_get_contents($path))) {
                    Log::warning('Database is backed up but not uploaded.', [
                        'file' => $path,
                    ]);
                }
            })->catch(fn (Throwable $e) => Log::error($e));

            return response()->noContent();
        } catch (UnableToWriteFile $e) {
            Log::error($e->getMessage());
        } catch (Exception $e) {
            Log::error($e);

            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

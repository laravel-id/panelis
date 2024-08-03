<?php

namespace App\Http\Controllers;

use App\Jobs\Database\UploadToCloud;
use App\Services\Database\Database;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use League\Flysystem\UnableToWriteFile;

class DatabaseController extends Controller
{
    public function backup(Request $request, Database $db): Response
    {
        if (! $request->user()->can('BackupDb')) {
            return response(__('Forbidden.'), Response::HTTP_FORBIDDEN);
        }

        try {
            $path = $db->backup();

            // upload to the cloud
            UploadToCloud::dispatch($path);

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

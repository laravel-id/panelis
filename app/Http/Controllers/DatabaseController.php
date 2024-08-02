<?php

namespace App\Http\Controllers;

use App\Services\Database\Database;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DatabaseController extends Controller
{
    public function backup(Database $db): Response
    {
        try {
            $db->backup();

            return response()->noContent();
        } catch (\Exception $e) {
            Log::error($e);
        }

        return response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

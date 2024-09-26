<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\Transaction\ProcessMootaJob;
use Illuminate\Http\Request;

class MootaController extends Controller
{
    public function __invoke(Request $request)
    {
        foreach ($request->all() as $mutation) {
            if ($mutation['type'] === 'CR') {
                dispatch(new ProcessMootaJob($mutation));
            }
        }

        return response('OK');
    }
}

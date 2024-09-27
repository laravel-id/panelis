<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PingController extends Controller
{
    public function __invoke(): Response
    {
        return response('Pong!');
    }
}

<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class Ping
{
    use AsAction;

    public function handle(?string $url): void
    {
        if (empty($url)) {
            Log::warning('Ping URL is not defined.');

            return;
        }

        Http::throw()->get($url);
    }
}

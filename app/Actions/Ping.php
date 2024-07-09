<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class Ping
{
    use AsAction;

    public function handle(string $url): void
    {
        Http::throw()->get($url);
    }
}

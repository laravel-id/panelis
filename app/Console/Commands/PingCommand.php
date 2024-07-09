<?php

namespace App\Console\Commands;

use App\Actions\Ping;
use Illuminate\Console\Command;

class PingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping to a URL';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Ping::run(config('healthcheck.ping'));
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Psr\Http\Message\UriInterface;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sitemap generator';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        SitemapGenerator::create(config('app.url'))
            ->shouldCrawl(function (UriInterface $uri): bool {
                return !str_contains($uri->getPath(), '/go');
            })
            ->writeToFile(public_path('sitemap.xml'));

        return 0;
    }
}

<?php

namespace App\Models\SimpleAnalytics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Page extends Model
{
    use HasFactory;
    use Sushi;

    public function getRows(): array
    {
        $pages = $this->getData();
        if (empty($pages)) {
            return [];
        }

        return $pages['pages'];
    }

    private function getData(): array
    {
        // cache using preferred driver instead of using built-in cache
        return Cache::remember('widget.top_page', now()->addMinutes(10), function (): array {
            $host = config('services.simple_analytics.host', 'https://simpleanalytics.com/schedules.run.json');

            $response = Http::withHeader('Api-Key', config('services.simple_analytics.api_key'))
                ->get($host, [
                    'start' => 'today-7d',
                    'end' => 'today',
                    'fields' => 'pages',
                    'version' => 5,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        });
    }
}

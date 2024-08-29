<?php

namespace App\Filament\Widgets\SimpleAnalytics;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()->can('ViewStatsChartWidget');
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('widget.sa_stat_chart_heading');
    }

    public function getDescription(): ?string
    {
        return __('widget.sa_stats_chart_description');
    }

    protected function getData(): array
    {
        $stats = $this->getStatsData();
        if (empty($stats['histogram'])) {
            return [];
        }

        $histograms = collect($stats['histogram']);

        return [
            'datasets' => [
                [
                    'label' => 'Visitor',
                    'data' => $histograms->pluck('visitors')->all(),
                    'borderColor' => '#C7253E',
                ],
                [
                    'label' => 'Page Views',
                    'data' => $histograms->pluck('pageviews')->all(),
                    'borderColor' => '#FABC3F',
                ],
            ],
            'labels' => $histograms
                ->pluck('date')
                ->map(fn (string $date): string => Carbon::parse($date)->translatedFormat('d M'))
                ->all(),
        ];
    }

    private function getStatsData(): array
    {
        $key = sprintf('widget.stats_chart.%s', sha1(serialize($this->filters)));

        return Cache::remember($key, now()->addMinutes(10), function (): array {
            $start = now(get_timezone())->subDays(7)->toDateString();
            $end = now(get_timezone())->toDateString();

            if (! empty($this->filters['started_at'])) {
                $start = Carbon::parse($this->filters['started_at'])->toDateString();
            }

            if (! empty($this->filters['ended_at'])) {
                $end = Carbon::parse($this->filters['ended_at'])->toDateString();
            }

            $host = config('services.simple_analytics.host', 'https://simpleanalytics.com/schedules.run.json');
            $response = Http::withHeader('Api-Key', config('services.simple_analytics.api_key'))
                ->get($host, [
                    'timezone' => get_timezone(),
                    'version' => 5,
                    'fields' => 'histogram',
                    'start' => $start,
                    'end' => $end,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        });
    }
}

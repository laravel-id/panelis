<?php

namespace App\Filament\Widgets\SimpleAnalytics;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ReferralChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?array $options = [
        'scales' => [
            'y' => [
                'grid' => [
                    'display' => false,
                ],
                'ticks' => [
                    'display' => false,
                ],
            ],
            'x' => [
                'grid' => [
                    'display' => false,
                ],
                'ticks' => [
                    'display' => false,
                ],
            ],
        ],
    ];

    public function getHeading(): string|Htmlable|null
    {
        return __('widget.sa_referrals');
    }

    protected function getFilters(): ?array
    {
        return [
            '' => __('widget.sa_filter_default'),
            'today' => __('widget.sa_filter_today'),
            'week' => __('widget.sa_filter_week'),
            'month' => __('widget.sa_filter_month'),
        ];
    }

    protected function getData(): array
    {
        $key = sprintf('widget.referral_chart.%s', $this->filter);
        $data = Cache::remember($key, now()->addHour(), function (): array {
            $host = config('services.simple_analytics.host', 'https://simpleanalytics.com/schedules.run.json');
            $response = Http::withHeader('Api-Key', config('services.simple_analytics.api_key'))
                ->get($host, [
                    'version' => 5,
                    'fields' => 'referrers',
                    'timezone' => get_timezone(),
                    'start' => match ($this->filter) {
                        null, '', 'default' => 'today-7d',
                        'today' => 'today',
                        'week' => 'today-7d',
                        'month' => 'today-30d',
                    },
                    'end' => 'today',
                ]);

            if (! empty($response->json('referrers'))) {
                return $response->json('referrers');
            }

            return [];
        });

        $referrers = collect($data);

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => $referrers->pluck('pageviews')->all(),
                    'backgroundColor' => $referrers
                        ->map(function (array $referrer): ?string {
                            $brand = str_replace('.', '', $referrer['value']);

                            return config(sprintf('brand.%s_color', $brand), '#FFBE98');
                        })
                        ->all(),
                ],
            ],
            'labels' => $referrers->pluck('value')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

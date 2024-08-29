<?php

namespace App\Filament\Widgets\SimpleAnalytics;

use App\Filament\Widgets\SimpleAnalytics\Enums\StatsFilter;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
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

    public static function canView(): bool
    {
        return Auth::user()->can('ViewReferralChartWidget');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('widget.sa_referrals');
    }

    protected function getFilters(): ?array
    {
        return collect(StatsFilter::cases())
            ->mapWithKeys(fn (StatsFilter $filter): array => [$filter->value => $filter->label()])
            ->all();
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
                    'start' => StatsFilter::tryFrom($this->filter)?->filter(),
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

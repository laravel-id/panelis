<?php

namespace App\Http\Controllers\Event;

use App\Actions\Schedule\GenerateCalendarUrl;
use App\Http\Controllers\Controller;
use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use App\Models\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    private string $timezone;

    public function __construct()
    {
        $this->timezone = get_timezone();

        view()->share('timezone', $this->timezone);
    }

    public function view(string $slug): View|RedirectResponse
    {
        // redirect old slug to a new one
        $history = Slug::query()
            ->where('origin', $slug)
            ->orderByDesc('created_at')
            ->first();
        if (! empty($history)) {
            return redirect(route('schedule.view', $history->destination), $history->status);
        }

        $schedule = Schedule::getScheduleBySlug($slug);
        set_locale($schedule->metadata['locale'] ?? app()->getLocale());

        $organizers = $schedule->organizers
            ->map(function (Organizer $organizer): string {
                return Str::of(sprintf('[%s](%s)', $organizer->name, route('organizer.view', $organizer->slug)))
                    ->inlineMarkdown()
                    ->replace(PHP_EOL, '')
                    ->toHtmlString();
            })
            ->implode(', ');

        $year = $schedule->started_at
            ->timezone($this->timezone)
            ->format('Y');

        $startedAt = $schedule->started_at->timezone($this->timezone);

        // find schedules with same date
        $relatedSchedules = Schedule::getPublishedSchedules([
            'date' => $schedule->started_at->timezone(get_timezone())->format('Y-m-d'),
            $excludes = 'excludes' => [
                // do not include the schedule itself
                'id' => [$schedule->id],
            ],
        ]);

        return view('pages.schedules.view')
            ->with(compact(
                'schedule',
                'startedAt',
                'organizers',
                'relatedSchedules',
            ))
            ->with('calendar', GenerateCalendarUrl::run($schedule))
            ->with('externalUrl', $schedule->external_url)
            ->with('structuredData', $this->generateStructuredData($schedule))
            ->with('title', sprintf('%s - %s', $schedule->title, $year));
    }

    public function filter(int $year, ?int $month = null, ?int $day = null): View
    {
        $date = now($this->timezone)
            ->setMonth($month)
            ->setYear($year);

        $title = vsprintf('%s %s', [
            $date->translatedFormat('F'),
            $year = $date->format('Y'),
        ]);

        if (empty($month)) {
            $title = $year;
        }

        return view('pages.schedules.filter')
            ->with('schedules', Schedule::getFilteredSchedules($year, $month, $day))
            ->with('pageTitle', __('event.schedules_in', ['time' => $title]))
            ->with('title', $title);
    }

    public function archive(): View
    {
        return view('pages.schedules.filter')
            ->with('schedules', Schedule::getArchivedSchedules())
            ->with('title', __('event.schedule_archive'));
    }

    private function generateStructuredData(Schedule $schedule): array
    {
        $schedule->load('district.region');

        $location = [
            '@type' => 'VirtualLocation',
        ];

        if (! $schedule->is_virtual) {
            $location = [
                '@type' => 'Place',
                'name' => $schedule->location,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $schedule->district?->name,
                    'addressRegion' => $schedule->district?->region?->name,
                    'addressCountry' => 'ID',
                ],
            ];
        }

        $offers = [];
        foreach ($schedule->packages as $package) {
            $offers[] = [
                '@type' => 'Offer',
                'name' => $package->title,
                'price' => $package->price,
                'priceCurrency' => 'IDR',
                'validFrom' => $package->started_at?->toIso8601String(),
                'url' => $package->url ?? $schedule->url,
                'availability' => $package->is_sold || $package->is_past || $schedule->is_past
                    ? 'https://schema.org/SoldOut'
                    : 'https://schema.org/InStock',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $schedule->title,
            'description' => Str::words(strip_tags($schedule->description), 200),
            'image' => $schedule->opengraph_image,
            'startDate' => $schedule->started_at->toIso8601String(),
            'endDate' => $schedule->finished_at?->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => $schedule->is_virtual
                ? 'https://schema.org/OnlineEventAttendanceMode'
                : 'https://schema.org/OfflineEventAttendanceMode',
            'location' => $location,
            'offers' => $offers,
        ];
    }
}

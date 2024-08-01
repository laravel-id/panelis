<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function view(string $slug): View
    {
        $schedule = Schedule::getScheduleBySlug($slug);

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

        $format = config('app.datetime_format', 'Y-m-d H:i:s');
        $dateFormat = str_replace(['H', 'i', 'g', 'G', 'u', ':', 'Y', 'y'], '', $format);

        // find schedules with same date
        $relatedSchedules = Schedule::getPublishedSchedules([
            'date' => $schedule->started_at->timezone(get_timezone())->format('Y-m-d'),
            $excludes = 'excludes' => [
                // do not include the schedule itself
                'id' => [$schedule->id],
            ],
        ]);

        // find schedule for next week
        $nextWeekSchedules = Schedule::getPublishedSchedules([
            'date' => $schedule->started_at->timezone(get_timezone())->addWeek()->format('Y-m-d'),
            $excludes,
        ]);

        return view('pages.schedules.view')
            ->with(compact(
                'schedule',
                'startedAt',
                'organizers',
                'format',
                'dateFormat',
                'relatedSchedules',
                'nextWeekSchedules',
            ))
            ->with('externalUrl', $schedule->external_url)
            ->with('title', sprintf('%s - %s', $schedule->title, $year));
    }

    public function index(): View
    {
        return view('pages.schedules.index');
    }

    public function filter(int $year, ?int $month = null): View
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
            ->with('schedules', Schedule::getFilteredSchedules($year, $month))
            ->with('title', $title);
    }

    public function archive(): View
    {
        return view('pages.schedules.filter')
            ->with('schedules', Schedule::getArchivedSchedules())
            ->with('title', __('event.schedule_archive'));
    }

    public function calendar(): View
    {
        return view('pages.schedules.calendar')
            ->with('title', __('event.schedule_calendar'));
    }

    public function json(Request $request): JsonResponse
    {
        return response()->json(
            Schedule::getPublishedSchedules()
                ->map(function (Schedule $schedule): array {
                    return [
                        'title' => $schedule->title,
                        'start' => $schedule
                            ->started_at
                            ->timezone($this->timezone)
                            ->format('Y-m-d H:i'),
                        'end' => $schedule
                            ->finished_at
                            ?->timezone($this->timezone)
                            ?->format('Y-m-d H:i') ?? null,
                        'url' => route('schedule.view', $schedule->slug),
                        'allDay' => false,
                    ];
                })
                ->toArray(),
        );
    }
}

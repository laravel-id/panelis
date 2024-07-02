<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        if (empty($schedule->external_link) && ! $schedule->is_past) {
            Log::warning('Missing external link for event.', [
                'title' => $schedule->title,
            ]);
        }

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

        return view('schedules.view')
            ->with(compact(
                'schedule',
                'startedAt',
                'organizers',
                'format',
                'dateFormat',
            ))
            ->with('title', sprintf('%s - %s', $schedule->title, $year));
    }

    public function index(Request $request): View
    {
        return view('schedules.index')
            ->with('schedules', Schedule::getPublishedSchedules($request->toArray()))
            ->with('search', true);
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

        return view('schedules.index')
            ->with('schedules', Schedule::getFilteredSchedules($year, $month))
            ->with('title', $title);
    }

    public function archive(): View
    {
        return view('schedules.index')
            ->with('schedules', Schedule::getArchivedSchedules())
            ->with('title', __('event.schedule_archive'));
    }

    public function calendar(): View
    {
        return view('schedules.calendar')
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

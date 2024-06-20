<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    private string $timezone = 'UTC';

    public function __construct()
    {
        $this->timezone = config('app.datetime_timezone', config('app.timezone'));
    }

    /**
     * When someone/thing come from legacy URL, redirects to a new one.
     *
     * @return RedirectResponse
     */
    public function viewLegacy(int $year, string $slug): RedirectResponse
    {
        return redirect()
            ->route('schedule.view', compact('slug'), Response::HTTP_PERMANENTLY_REDIRECT);
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

        $year = $schedule->created_at
            ->timezone($this->timezone)
            ->format('Y');

        $format = config('app.datetime_format', 'd M Y H:i');
        $dateFormat = str_replace(['H', 'i', 'g', 'G', 'u', ':', 'Y', 'y'], '', $format);

        return view('schedules.view')
            ->with(compact(
                'schedule',
                'year',
                'organizers',
                'format',
                'dateFormat',
            ))
            ->with('timezone', $this->timezone)
            ->with('title', sprintf('%s - %s', $schedule->title, $year));
    }

    public function index(Request $request): View
    {
        return view('schedules.index')
            ->with('timezone', $this->timezone)
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

    public function go(Request $request): RedirectResponse
    {
        if (empty($request->get('url'))) {
            return redirect()->route('index');
        }

        return redirect()->to($request->get('url'));
    }
}

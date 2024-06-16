<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function view(int $year, string $slug): View
    {
        $schedule = Schedule::getScheduleByYearAndSlug($year, $slug);

        $organizers = $schedule->organizers
            ->map(function (Organizer $organizer): string {
                return Str::of(sprintf('[%s](%s)', $organizer->name, route('organizer.view', $organizer->slug)))
                    ->inlineMarkdown()
                    ->replace(PHP_EOL, '')
                    ->toHtmlString();
            })
            ->implode(', ');

        return view('schedules.view', compact('schedule', 'year', 'organizers'))
            ->with('format', config('app.datetime_format', 'd M Y H:i'))
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
        return view('schedules.index')
            ->with('schedules', Schedule::getFilteredSchedules($year, $month))
            ->with('title', __('event.schedule_archive'));
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

<?php

namespace App\Http\Controllers;

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\View\View;

class OrganizerController extends Controller
{
    public function view(Organizer $organizer): View
    {
        $schedules = Schedule::query()
            ->whereRelation('organizers', 'id', $organizer->id)
            ->orderByDesc('started_at')
            ->get();

        return view('organizers.view', compact('organizer', 'schedules'))
            ->with('title', $organizer->name);
    }
}

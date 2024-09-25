<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event\Participant;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function view(Participant $participant): View
    {
        $participant->load(['schedule', 'package']);

        return view('pages.participants.view', compact('participant'));
    }

    public function status(Participant $participant): View
    {
        $participant->load(['payment', 'schedule']);

        seo()->title(__('event.participant_status'), false);

        return view('pages.participants.status')
            ->with('participant', $participant)
            ->with('schedule', $participant->schedule);
    }
}

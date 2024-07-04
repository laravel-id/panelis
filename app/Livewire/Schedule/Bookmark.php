<?php

namespace App\Livewire\Schedule;

use App\Models\Calendar;
use App\Models\Event\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Bookmark extends Component
{
    public bool $bookmarked = false;

    public Schedule $schedule;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->bookmarked = Calendar::query()
                ->where([
                    'user_id' => Auth::id(),
                    'schedule_id' => $this->schedule->id,
                ])
                ->exists();
        }
    }

    public function render()
    {
        return view('livewire.schedule.bookmark');
    }

    public function mark(): void
    {
        if (!$this->bookmarked) {
            $calendar = $this->schedule->calendars()
                ->where('user_id', Auth::id())
                ->withTrashed()
                ->first();

            if (!empty($calendar)) {
                $calendar->restore();
            } else {
                $this->schedule->calendars()->create([
                    'user_id' => Auth::id(),
                ]);
            }

            $this->bookmarked = true;
        }
    }

    public function unmark(): void
    {
        $this->schedule->calendars()
            ->where('user_id', Auth::id())
            ->where([
                'user_id' => Auth::id(),
            ])
            ->delete();

        $this->bookmarked = false;
    }
}

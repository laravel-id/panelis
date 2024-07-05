<?php

namespace App\Livewire\Schedule;

use App\Livewire\Forms\Schedule\ReportForm;
use App\Models\Event\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class Toolbar extends Component
{
    public Schedule $schedule;

    public ReportForm $reportForm;

    public bool $marked = false;

    #[Computed]
    public function count(): int
    {
        return $this->schedule->bookmarks()->count();
    }

    public function mount(): void
    {
        if (Auth::check()) {
            $this->marked = $this->schedule
                ->bookmarks()
                ->where('user_id', Auth::id())
                ->exists();
        }
    }

    public function bookmark(): void
    {
        $bookmark = $this->schedule->bookmarks()
            ->where('user_id', Auth::id())
            ->withTrashed()
            ->first();

        if (!empty($bookmark)) {
            if ($bookmark->trashed()) {
                $bookmark->restore();
                $this->marked = true;
            } else {
                $bookmark->delete();
                $this->marked = false;
            }

            return;
        }

        $this->schedule->bookmarks()->create(['user_id' => Auth::id()]);
        $this->marked = true;
    }

    /**
     * @throws ValidationException
     */
    public function report(): void
    {
        $this->reportForm->store($this->schedule);
        $this->dispatch('reported');
    }

    public function render(): View
    {
        return view('livewire.schedule.toolbar');
    }
}

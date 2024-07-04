<?php

namespace App\Livewire\Schedule;

use App\Models\Event\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Report extends Component
{
    public string $modal = 'closed';

    public Schedule $schedule;

    public bool $bookmarked = false;

    #[Validate('required')]
    public bool $anonymous = true;

    #[Validate('required')]
    public string $message = '';
    public function render()
    {
        return view('livewire.schedule.report');
    }

    public function submit(): void
    {
        $this->validate();

        $this->schedule->reports()->create([
            'user_id' => !$this->anonymous ? Auth::id() ?? null : null,
            'message' => $this->message,
        ]);

        $this->reset(['message']);

        $this->toggleModal();
    }

    public function toggleModal(): void
    {
        if ($this->modal === 'open') {
            $this->modal = 'closed';
        } else {
            $this->modal = 'open';
        }
    }
}

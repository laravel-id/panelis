<?php

namespace App\Livewire\Schedule;

use App\Models\Event\Event;
use App\Models\Event\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Lazy]
class Index extends Component
{
    public ?Collection $schedules = null;

    #[Url]
    public ?string $keyword = '';

    #[Url]
    public bool $virtual = false;

    #[Url]
    public bool $past = false;

    #[Url]
    public ?string $date = null;

    private function getFilteredSchedules(): ?Collection
    {
        return Event::getPublishedSchedules($this->only([
            'keyword',
            'virtual',
            'past',
            'date',
        ]));
    }

    public function mount(): void
    {
        $this->schedules = $this->getFilteredSchedules();
    }

    public function updated(): void
    {
        $this->schedules = $this->getFilteredSchedules();
    }

    public function render()
    {
        return view('livewire.schedule.index');
    }
}

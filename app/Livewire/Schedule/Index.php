<?php

namespace App\Livewire\Schedule;

use App\Models\Event\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
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
        $events = Event::getPublishedSchedules($this->only([
            'keyword',
            'virtual',
            'past',
            'date',
        ]));

        $pinnedEvent = Event::getPinnedSchedule();
        if (! empty($pinnedEvent)) {
            $events->prepend($pinnedEvent);
        }

        return $events;
    }

    public function mount(): void
    {
        $this->schedules = $this->getFilteredSchedules();
    }

    public function updated(): void
    {
        $this->schedules = $this->getFilteredSchedules();
    }

    public function render(): View
    {
        return view('livewire.schedule.index')
            ->extends('layouts.app');
    }
}

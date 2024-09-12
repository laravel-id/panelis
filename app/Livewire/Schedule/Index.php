<?php

namespace App\Livewire\Schedule;

use App\Models\Event\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public ?Collection $schedules = null;

    #[Url(history: true)]
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

        $pinnedEvent = Cache::remember('event.pinned', now()->addHour(), function (): ?Event {
            return Event::getPinnedSchedule();
        });

        if (!empty($pinnedEvent)) {
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
            ->extends('layouts.app')
            ->title(config('app.title'));
    }
}

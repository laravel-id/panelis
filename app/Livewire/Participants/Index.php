<?php

namespace App\Livewire\Participants;

use App\Enums\Participants\Status;
use App\Models\Event\Participant;
use App\Models\Event\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public ?Schedule $schedule = null;

    public ?Participant $selectedParticipant = null;

    #[Url(history: true)]
    public string $keyword = '';

    public bool $openDialog = false;

    public function mount(string $slug): void
    {
        $this->schedule = Schedule::getScheduleBySlug($slug);
    }

    public function complete(string $ulid): void
    {
        $participant = Participant::query()
            ->where('ulid', $ulid)
            ->firstOrFail();

        $participant->status = Status::Completed;
        $participant->save();

        $this->close();
    }

    public function view(string $ulid): void
    {
        $this->selectedParticipant = Participant::query()
            ->where('ulid', $ulid)
            ->first();

        $this->openDialog = true;
    }

    public function close(): void
    {
        $this->openDialog = false;
        $this->reset('selectedParticipant');
    }

    public function render(): View
    {
        abort_if(empty($this->schedule) || $this->schedule->is_past, Response::HTTP_NOT_FOUND);

        $this->schedule->load([
            'participants.package',
            'participants' => function (HasMany $query): HasMany {
                return $query->IsFulfilled()
                    ->when(! empty($this->keyword), function (Builder $builder): Builder {
                        return $builder->where('name', 'like', '%'.$this->keyword.'%')
                            ->orWhere('bib', 'like', '%'.$this->keyword.'%');
                    });
            },
        ]);

        seo()->title(__('event.schedule_participant', ['title' => $this->schedule->title]));

        return view('livewire.participants.index')
            ->extends('layouts.app')
            ->with('schedule', $this->schedule);
    }
}

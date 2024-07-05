<?php

namespace App\Livewire\Forms\Schedule;

use App\Models\Event\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReportForm extends Form
{
    #[Validate('required')]
    public bool $anonymous = true;

    #[Validate('required|min:10')]
    public string $message = '';

    /**
     * @throws ValidationException
     */
    public function store(Schedule $schedule): void
    {
        $this->validate();

        $schedule->reports()->create([
            'user_id' => $this->anonymous ? null : Auth::user()?->id ?? null,
            'message' => $this->message,
        ]);

        $this->reset();
    }
}

<?php

namespace App\Livewire\User;

use Illuminate\View\View;
use Livewire\Component;

class Event extends Component
{
    public function render(): View
    {
        return view('livewire.user.event')
            ->title(__('user.my_event'));
    }
}

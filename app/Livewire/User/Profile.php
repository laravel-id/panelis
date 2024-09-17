<?php

namespace App\Livewire\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class Profile extends Component
{
    #[Validate('required|min:3|max:100')]
    public string $name = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function rules(): array
    {
        return [
            'password' => [
                'nullable',
                Password::min(8),
            ],
        ];
    }

    public function update(): Redirector|RedirectResponse
    {
        $this->validate();

        $user = Auth::user();
        $user->name = $this->name;
        if (! empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $this->reset('password');

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.user.profile')
            ->extends('layouts.app')
            ->title(__('user.profile'));
    }
}

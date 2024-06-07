<?php

namespace App\Filament\Pages\Auth;

class Login extends \Filament\Pages\Auth\Login
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => env('DEMO_EMAIL'),
            'password' => env('DEMO_PASSWORD'),
        ]);
    }
}

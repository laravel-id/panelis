<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

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

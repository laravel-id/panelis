<?php

namespace App\Livewire\User;

use App\Models\Setting as SettingModel;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Enums\ThemeMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Setting extends Component
{
    #[Validate]
    public string $language = '';

    #[Validate]
    public string $color = '';

    public function mount(): void
    {
        $this->language = config('app.locale');
        $this->color = config('color.mode', 'light');
    }

    public function rules(): array
    {
        return [
            'language' => [
                'required',
                Rule::in(LanguageSwitch::make()->getLocales()),
            ],
            'color' => [
                'required',
                Rule::in(array_column(ThemeMode::cases(), 'value')),
            ],
        ];
    }

    public function update(): Redirector|RedirectResponse
    {
        $this->validate();

        SettingModel::set(
            key: 'app.locale',
            value: $this->language,
            userId: Auth::id(),
        );

        SettingModel::set(
            key: 'color.mode',
            value: $this->color,
            userId: Auth::id(),
        );

        // full-page reload
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.user.setting')
            ->with('locales', LanguageSwitch::make()->getLocales())
            ->extends('layouts.app')
            ->title(__('user.setting'));
    }
}

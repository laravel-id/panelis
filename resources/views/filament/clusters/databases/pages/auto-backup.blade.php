<x-filament-panels::page>
    <x-filament-panels::form wire:submit="update">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit" size="sm" :disabled="$isButtonDisabled">
                @lang('ui.btn.save')
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <x-filament-actions::modals />
</x-filament-panels::page>

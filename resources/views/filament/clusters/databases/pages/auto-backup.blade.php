<x-filament-panels::page>
    <form wire:submit="update">
        <div class="mb-10">{{ $this->form }}</div>

        <x-filament::button type="submit" size="sm" class="mt-10 bm-10" :disabled="$isButtonDisabled ?? false">
            @lang('ui.button_save')
        </x-filament::button>
    </form>
    <x-filament-actions::modals />
</x-filament-panels::page>

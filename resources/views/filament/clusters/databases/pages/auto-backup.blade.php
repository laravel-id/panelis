<x-filament-panels::page>
    {{ $this->form }}

    @if ($this->isSupported)
    <div>
        <div class="mt-6">
            {{ $this->getUpdateAction() }}
        </div>
    </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>

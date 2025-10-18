<x-filament-panels::page>
    {{ $this->form }}

    <div>
        <div class="mt-6">
            {{ $this->getUpdateAction() }}
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>

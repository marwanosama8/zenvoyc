<x-layouts.filament-view>
    <livewire:view-offer :token="$token" />
    <x-slot name="title">
        {{ __('offer') }}
    </x-slot>
</x-layouts.filament-view>

<x-layouts.filament-view>
    <livewire:customer-invoices :token="$token" />
    <x-slot name="title">
        {{ __('invoice.customer-invoices-list') }}
    </x-slot>
</x-layouts.filament-view>

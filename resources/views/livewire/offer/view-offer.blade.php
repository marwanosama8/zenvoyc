@use('App\Helpers\Helpers')
@php
    $currency = Helpers::getCurrancyData($providerArray['currency_id']);
    $lang = $providerArray['invoice_language'];
@endphp

{{-- Page Container --}}
<div class="container max-w-5xl p-10 mx-auto bg-white border-2 border-gray-300 rounded-lg shadow-lg">

    {{-- Header --}}
    <header class="flex flex-col items-center mb-8">
        <img class="w-48 mb-4" src="{{ asset('storage/' . $providerArray['avatar_url']) }}" alt="Company Logo" />
        <h1 class="mb-2 text-4xl font-bold">{{ $providerArray['legal_name'] }}</h1>
        <p class="text-sm text-gray-600">{{ $providerArray['address'] }}</p>
    </header>

    {{-- Date and Contract Information --}}
    <div class="flex justify-between mb-6 text-gray-700">
        <div>
            <p>Date: {{ $offer->created_at->format('d.m.Y') }}</p>
        </div>
        <div class="text-right">
            <p>Contact: {{ $providerArray['contact_number'] }}</p>
            <p>Email: <a href="mailto:{{ $providerArray['contact_email'] }}" class="text-blue-500">{{ $providerArray['contact_email'] }}</a></p>
            <p>Website: <a href="{{ $providerArray['website_url'] }}" class="text-blue-500" target="_blank">{{ $providerArray['website_url'] }}</a></p>
        </div>
    </div>

    {{-- Offer Title --}}
    <div class="my-8 text-center">
        <h2 class="text-3xl font-bold">{{ $offer->title }}</h2>
        <div class="w-24 h-1 mx-auto mt-4 bg-gray-400"></div>
    </div>

    {{-- Introduction --}}
    <div class="mt-8 text-gray-700">
        <h3 class="mb-4 text-xl font-semibold">Dear {{ $offer->customer()->withoutGlobalScopes()->first()->name }},</h3>
        <p>{!! $offer->introtext !!}</p>
    </div>

    {{-- Positions Table --}}
    <div class="mt-8">
        <h3 class="mb-4 text-2xl font-semibold text-center">Positions</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead class="text-white bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3">{{ __('offer-contract.description', locale: $lang) }}</th>
                        <th class="px-6 py-3">{{ __('offer-contract.units', locale: $lang) }}</th>
                        <th class="px-6 py-3">{{ __('offer-contract.price_in', locale: $lang) . ' ' . $currency->symbol }}</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50">
                    @foreach ($offer->positions as $key => $item)
                        <tr class="border-b">
                            <td class="px-6 py-4">{{ $key + 1 }}</td>
                            <td class="px-6 py-4">{{ nl2br(e($item['description'])) }}</td>
                            <td class="px-6 py-4">
                                @switch($item['type'])
                                    @case(1)
                                        {{ __('offer-contract.price_in', locale: $lang) }}
                                        @break
                                    @case(2)
                                        {{ $item['amount'] > 1 ? $item['amount'] . ' ' . __('offer-contract.hours', locale: $lang) : $item['amount'] . ' ' . __('offer-contract.hour', locale: $lang) }}
                                        @break
                                    @default
                                        {{ __('offer-contract.pauschal', locale: $lang) }}
                                @endswitch
                            </td>
                            <td class="px-6 py-4">{{ number_format($item['price'], 2, ',', '.') . ' ' . $currency->symbol }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Signature Section --}}
    <div class="mt-12 text-center">
        <h2 class="text-2xl font-bold">{{ __('offer-contract.the_signture', locale: $lang) }}</h2>
        @if (!$offer->signed)
            <p class="mt-2 text-gray-600">{{ __('offer-contract.once_you_sign_this_contract_and_submit_it_you_cant_edit_id', locale: $lang) }}</p>
        @endif
        <form wire:submit="create" class="mt-8">
            {{ $this->form }}
            @if (!$offer->signed)
                <x-filament::modal>
                    <x-slot name="trigger">
                        <x-filament::button wire:click='trigger' class="px-4 py-2 mt-4 text-white bg-green-600">
                            {{ __('offer-contract.submit', locale: $lang) }}
                        </x-filament::button>
                    </x-slot>
                    <p class="mb-4">{{ __('offer-contract.are_you_sure_you_want_to_submit_this_signture', locale: $lang) }}</p>
                    <div class="flex justify-end gap-4 mt-4">
                        <x-filament::button type="submit" class="px-4 py-2 text-white bg-green-500">
                            {{ __('offer-contract.submit', locale: $lang) }}
                        </x-filament::button>
                    </div>
                </x-filament::modal>
            @endif
        </form>
    </div>

    {{-- Footer --}}
    <footer class="pt-4 mt-12 text-xs text-center text-gray-600 border-t-2 border-gray-300">
        <p>© {{ now()->year }} {{ $providerArray['legal_name'] }}. All rights reserved.</p>
        <p>VAT ID: {{ $providerArray['vat_id'] }} | Tax ID: {{ $providerArray['tax_id'] }}</p>
    </footer>
</div>

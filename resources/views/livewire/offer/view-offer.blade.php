{{-- body --}}

<div class="container p-10 m-10 mx-auto bg-gray-100 border-2 border-black">
    <div class="pb-3 text-sm">
        Date: {{ $offer->created_at->format('d.m.Y') }}
    </div>
    <div class="flex justify-between ">
        <div class="float-left w-4/6 px-4 mr-4">
            <img class="mt-5 ml-10" style="max-width: 430px" src="{{ asset('storage/' . $provider['avatar_url']) }}" />
        </div>
        <div class="relative float-left w-1/3 px-4">
            <br />
            <strong>{{$provider['legal_name']}}</strong>
            <br />
                {!! $provider['address'] !!}
            <br />
        </div>

    </div>

    <div class="flex items-center justify-center">
        <div class="h-0.5 w-1/3 mx-4 bg-gray-400"></div>
    </div>

    {{-- offer title --}}
    <br />
    <div class="flex flex-col items-center my-8">
        <div class="w-full text-xl font-bold text-center">
            <h1>{{ $offer->title }}</h1>
        </div>
        <div class="flex items-center justify-center">
            <div class="h-0.5 w-1/3 mx-4 bg-gray-400 py-3"></div>
        </div>
    </div>
    <br />
    {{-- end offer title --}}

    {{-- offer content --}}
    <div class="flex flex-col gap-4">
        <h1>Dear {{ $offer->customer()->withoutGlobalScopes()->first()->name }},</h1>
        <div>
            <style>
                ol {
                    list-style-type: decimal;
                } 

                ul {
                    list-style-type: disc;
                }
            </style>
            {!! $offer->introtext !!}

        </div>
    </div>
    <div class="flex items-center justify-center my-4">
        <div class="h-0.5 w-1/3 mx-4 bg-gray-400"></div>
    </div>
    {{-- end offer content --}}

    {{-- postions --}}

    <div class="mt-8 text-xl text-center">
        <h1 class="font-bold">Postions</h1>
    </div>

    <div class="flex flex-col mx-4 mt-4">
        <div class="max-w-full overflow-x-auto">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="w-full mx-auto text-xs font-light text-center sm:text-sm">
                        <thead
                            class="font-medium text-white bg-blue-800 border-b dark:border-neutral-500 dark:bg-neutral-900">
                            <tr>
                                <th scope="col" class="px-6 py-4"> # </th>
                                <th scope="col" class="px-6 py-4 text-center"> Beschreibung </th>
                                <th scope="col" class="px-6 py-4"> Einheiten </th>
                                <th scope="col" class="px-6 py-4"> Preis in € </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($offer->positions as $key => $item)
                                <tr class="max-w-xs text-sm border-b dark:border-neutral-500">
                                    <td class="px-6 py-4 font-medium text-left whitespace-nowrap">
                                        {{ $key + 1 }} </td>
                                    <td class="px-6 py-4 whitespace-normal"> {!! nl2br($item['description']) !!} </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($item['type'])
                                            @case(1)
                                                pauschal
                                            @break

                                            @case(2)
                                                @if ($item['amount'] > 1)
                                                    {{ $item['amount'] }} Stunden
                                                @else
                                                    {{ $item['amount'] }} Stunde
                                                @endif
                                            @break

                                            @default
                                                pauschal
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($item['price'], 2, ',', '.') }} € </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-center">
        <div class="h-0.5 w-1/3 mx-4 bg-gray-400"></div>
    </div>
    {{-- end postions --}}

    <div class="flex flex-col gap-5 my-8 text-center">

        <h1 class="text-xl font-bold">The Signture</h1>
        @if (!$offer->signed)
            <h4 class="text-gray-500">Once you submit your signutre you can't edit it again</h4>
        @endif
        <form wire:submit="create">
            {{ $this->form }}

            @if (!$offer->signed)
                <div class="mt-8 ">
                    <x-filament::modal>
                        <x-slot name="trigger">
                            <div>
                                <x-filament::button wire:click='trigger' class="bg-green-500">
                                    {{ __('offer.submit') }}
                                </x-filament::button>
                            </div>
                        </x-slot>
                        Are you sure you want to submit this signture
                        <div class="flex justify-end gap-3">
                            <x-filament::button type='submit' class="bg-green-400">
                                Submit
                            </x-filament::button>
                        </div>
                    </x-filament::modal>
                </div>
            @endif
        </form>
    </div>


</div>
{{-- end body --}}

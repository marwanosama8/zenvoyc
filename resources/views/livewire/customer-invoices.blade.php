<div class="h-full min-h-screen mx-auto bg-gray-200 ">
    <div class="container flex flex-col mx-auto pt-28">
        <div class="flex flex-row gap-4">
            @foreach ($lastThreeYears as $key => $year)
                <div class="mb-4">
                    <button wire:click='changeYear({{ $year }})' @class(['p-4 rounded-md', 'bg-white' => $selectedYear == $year])>
                        {{ $year }}
                    </button>
                </div>
            @endforeach
        </div>
        {{ $this->table }}

        <x-filament-actions::modals />

    </div>
</div>


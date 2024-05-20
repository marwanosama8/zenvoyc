<div class=" mx-auto bg-gray-200 h-full min-h-screen">
    <div class="container mx-auto pt-28 flex flex-col">
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
    </div>
</div>

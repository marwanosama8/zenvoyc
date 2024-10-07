@php use App\Filament\Company\Resources\ExpenditureResource\Pages\ListExpenditures @endphp
<div class="expendture-widget">
    <x-filament-widgets::widget class="expendture-widget">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('expendutire.widget.frequency') }}
            </x-slot>
            <div>
                <div class="mb-4">
                    <div class="flex space-x-4 ">
                        <label>
                            <x-filament::input.checkbox class="mr-2" value="one-time"
                                wire:model.live="selectedFrequency" />
                            <span>
                                {{ __('expendutire.widget.one_time') }}
                            </span>
                        </label>
                        <label>
                            <x-filament::input.checkbox class="mr-2" value="monthly"
                                wire:model.live="selectedFrequency" />
                            <span>
                                {{ __('expendutire.widget.monthly') }}
                            </span>
                        </label>
                        <label>
                            <x-filament::input.checkbox class="mr-2" value="yearly"
                                wire:model.live="selectedFrequency" />
                            <span>
                                {{ __('expendutire.widget.yearly') }}
                            </span>
                        </label>
                    </div>
                </div>
                <a href={{ ListExpenditures::getUrl(['activeTab' => count($selectedFrequency) > 1 ? 'All' : ucfirst($selectedFrequency[0] ?? 'All')]) }}
                    class="mt-6 text-2xl">
                    <h2>{{ __('expendutire.widget.total_cost') }}: <span class="font-bold">{{ $totalCost }}</span>
                    </h2>
                </a>
            </div>
        </x-filament::section>
    </x-filament-widgets::widget>
</div>

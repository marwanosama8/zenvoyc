<x-filament::page>
    <x-filament::tabs label="Content">
        <x-filament::tabs.item :active="$activeTap === 1" wire:click="$set('activeTap', 1)">
            {{ __('company-settings.profile-settings.top_heading') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTap === 2" wire:click="$set('activeTap', 2)">
            {{ __('company-settings.invoice-settings.top_heading') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$activeTap === 3" wire:click="$set('activeTap', 3)">
            {{ __('company-settings.mail-configrations-settings.top_heading') }}
        </x-filament::tabs.item>

    </x-filament::tabs>



    {{-- Forms --}}
    @if ($activeTap == 1)
        {{-- tap 1 --}}
        <x-filament::card header="{{ __('company-settings.profile-settings.top_heading') }}">
            <form wire:submit.prevent="saveProfileSettingsForm">

                {{ $this->profileSettingsForm }}
           
                {{-- save --}}
                <div class="flex justify-end mt-8">
                    <x-filament::button type="submit">
                        <x-filament::loading-indicator class="inline w-5 h-5" wire:loading />
                        {{ __('save') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>
        {{-- end tap 1 --}}
    @endif
    @if ($activeTap == 2)
        {{-- tap 2 --}}
        <x-filament::card header="{{ __('company-settings.invoice-settings.top_heading') }}">
            <form wire:submit.prevent="saveInvoiceSettingsForm">
                {{ $this->invoiceSettingsForm }}

                {{-- save --}}
                <div class="flex justify-end mt-8">
                    <x-filament::button type="submit">
                        <x-filament::loading-indicator class="inline w-5 h-5" wire:loading />
                        {{ __('save') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>
        {{-- end tap 2 --}}
    @endif
    @if ($activeTap == 3)
        {{-- tap 2 --}}
        <x-filament::card header="{{ __('company-settings.mail-configrations-settings.top_heading') }}">
            <form wire:submit.prevent="saveMailConfigrationsSettingsForm">
                {{ $this->mailConfigrationsSettingsForm }}

                {{-- save --}}
                <div class="flex justify-end mt-8">
                    <x-filament::button type="submit">
                        <x-filament::loading-indicator class="inline w-5 h-5" wire:loading />
                        {{ __('save') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>
        {{-- end tap 2 --}}
    @endif
    {{-- End Forms --}}
</x-filament::page>

<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use App\Constants\InvoiceThemeConstants;
use App\Helpers\TenancyHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class Settings extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    public static function getNavigationLabel(): string
    {
        return __('navigation.settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.mangment');
    }
    protected static string $view = 'filament.dashboard.pages.settings';
    
    public $activeTap;
    
    public function mount()
    {
        $this->activeTap = request()->activeTap ?? 1;
        $userSettingArray = auth()->user()->settings()->first()?->toArray();
        // The Forms
        $this->profileSettingsForm->fill($userSettingArray);
        $this->invoiceSettingsForm->fill($userSettingArray);
    }


    public $invoiceSettingsValues;
    public $profileSettingsValues;

    public $profileSettings = [];
    public $invoiceSettings = [];

    protected function getForms(): array
    {
        return [
            'profileSettingsForm',
            'invoiceSettingsForm'
        ];
    }

    protected function profileSettingsForm(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General Information')
                            ->label(__('general_info'))
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '92:16'
                                    ])->label('Avatar URL')->nullable(),
                                TextInput::make('name')->label('Company Name'),
                                TextInput::make('managing_director')->label('Managing Director')->nullable(),
                                TextInput::make('legal_name')->label('Legal Name')->nullable(),
                                TextInput::make('website_url')->label('Website URL')->nullable(),
                                TextInput::make('place_of_jurisdiction')->label('Place of Jurisdiction')->nullable(),
                            ]),
                        Tabs\Tab::make('Address')
                            ->label(__('address'))
                            ->schema([
                                RichEditor::make('address')
                                    ->label('Address')->nullable(),
                                TextInput::make('postal_code')->label('Postal Code')->nullable(),
                            ]),
                        Tabs\Tab::make('Bank Information')
                            ->label(__('bank_info'))
                            ->schema([
                                TextInput::make('tax_id')->label('Tax ID')->nullable(),
                                TextInput::make('vat_id')->label('VAT ID')->nullable(),
                                TextInput::make('iban')->label('IBAN')->nullable(),
                                TextInput::make('account_number')->label('Account Number')->nullable(),
                                TextInput::make('bank_code')->label('Bank Code')->nullable(),
                                TextInput::make('bic')->label('BIC')->nullable(),
                            ]),
                        Tabs\Tab::make('Contact Information')
                            ->label(__('contact_address'))
                            ->schema([
                                TextInput::make('contact_number')->label('Contact Number')->nullable(),
                                TextInput::make('contact_email')->label('Contact Email')->nullable(),
                            ])
                    ])

            ])
            ->statePath('profileSettings');
    }

    protected function invoiceSettingsForm(Form $form): Form
    {

        return $form
            ->schema([
                Toggle::make('ready_to_generate')
                ->label('Start generating')
                ->hint('you must trigger this button then fill the below form to start generating invoices')
                ->live(),
                TextInput::make('vat_percent')
                ->numeric()
                ->minValue(1)
                ->maxValue(100)
                ->inputMode('decimal')
                ->label(__('vat_percent'))
                ->suffix('%')
                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                ->hint(__('Loream ismapnn aqwouzpo pdocvb'))
                ->required(),
                Select::make('invoice_language')->label(__('invoice_language'))
                    ->options([
                        'en' => 'English',
                        'de' => 'Dutch',
                    ])
                    ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                    ->suffixIcon('heroicon-m-language')
                    ->required(),

                Select::make('invoice_theme_id')
                    ->options(InvoiceThemeConstants::getFormattedThemes())
                    ->native(0)
                    ->required()
                    ->allowHtml()
                    ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                    ->label(__('invoice_theme'))->nullable(),
                Select::make('currency_id')
                    ->options(
                        \App\Models\Currency::all()->sortBy('name')
                            ->mapWithKeys(function ($currency) {
                                return [$currency->id => $currency->name . ' (' . $currency->symbol . ')'];
                            })
                            ->toArray()
                    )
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                    ->suffixIcon('heroicon-m-currency-dollar')
                    ->label(__('Currency')),
                Actions::make([
                    Action::make('preview')
                        ->label(__('Generate Preview'))
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->openUrlInNewTab()
                        ->modalContent(function () {
                            $url = route('fake.pdf', TenancyHelpers::isCompanyOrUser());

                            return new HtmlString('<iframe src="' . $url . '" class="w-full h-screen"></iframe>');
                        })

                ]),
            ])
            ->statePath('invoiceSettings');
    }


    public function saveProfileSettingsForm(): void
    {
        $data = $this->toKeyValueArray($this->profileSettingsForm->getState());
        auth()->user()->settings()->update($data);
        Notification::make()->title(__('settings.profile-settings.notification'))->success()->send();
    }

    public function saveInvoiceSettingsForm(): void
    {
        $data = $this->toKeyValueArray($this->invoiceSettingsForm->getState());
        auth()->user()->settings()->update($data);
        Notification::make()->title(__('settings.invoice-settings.notification'))->success()->send();
    }


    private function toKeyValueArray($data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }


    public function changeTap($tap)
    {
        $this->activeTap = $tap;
    }

    public function getHeading(): string
    {
        return __('settings_page_heading');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('user');
    }
}

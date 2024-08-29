<?php

namespace App\Filament\Company\Pages;

use App\Constants\InvoiceThemeConstants;
use App\Helpers\Helpers;
use App\Helpers\TenancyHelpers;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\HtmlString;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class CompanySettings extends Page
{

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.company.pages.company-settings';

    public function mount()
    {
        $companyProfileArray = TenancyHelpers::getTenant()->toArray();
        $companyInvoiceSettingArray = TenancyHelpers::getTenant()->settings()->first()?->toArray();
        // The Forms
        $this->profileSettingsForm->fill($companyProfileArray);
        $this->invoiceSettingsForm->fill($companyInvoiceSettingArray);
    }

    public static function getNavigationLabel(): string
    {
        return __('company-settings.page_title');
    }

    public $activeTap = 1;

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
                                    ])->label(__('Avatar URL'))->required(),
                                TextInput::make('name')->label(__('Company Name')),
                                TextInput::make('managing_director')->label(__('Managing Director'))->required(),
                                TextInput::make('legal_name')->label(__('Legal Name'))->required(),
                                TextInput::make('website_url')->label(__('Website URL'))->required(),
                                TextInput::make('place_of_jurisdiction')->label(__('Place of Jurisdiction'))->required(),
                            ]),
                        Tabs\Tab::make('Address')
                            ->label(__('address'))
                            ->schema([
                                RichEditor::make('address')
                                    ->label(__('Address'))->required(),
                                TextInput::make('postal_code')->label(__('Postal Code'))->required(),
                                Select::make('country_id')
                                    ->label(__('label.county_id'))
                                    ->options(Helpers::getPluckCountries())
                                    ->searchable()
                                    ->required(),
                                    TextInput::make('city')->label(__('city'))->required(),

                            ]),
                        Tabs\Tab::make('Bank Information')
                            ->label(__('bank_info'))
                            ->schema([
                                TextInput::make('tax_id')->label(__('Tax ID')),
                                TextInput::make('vat_id')->label(__('VAT ID')),
                                TextInput::make('iban')->label(__('IBAN'))->nullable(),
                                TextInput::make('account_number')->label(__('Account Number'))->nullable(),
                                TextInput::make('bank_code')->label(__('Bank Code'))->nullable(),
                                TextInput::make('bic')->label(__('BIC'))->nullable(),
                            ]),
                        Tabs\Tab::make('Contact Information')
                            ->label(__('contact_address'))
                            ->schema([
                                TextInput::make('contact_number')->label(__('Contact Number'))->nullable(),
                                TextInput::make('contact_email')->label(__('Contact Email'))->nullable(),
                            ])
                    ])

            ])
            ->statePath('profileSettings');
    }

    protected function invoiceSettingsForm(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('vat_percent')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->inputMode('decimal')
                    ->label(__('vat_percent'))
                    ->suffix('%')
                    ->hint(__('Loream ismapnn aqwouzpo pdocvb'))
                    ->required(),
                Select::make('invoice_language')
                    ->label(__('invoice_language'))
                    ->options([
                        'en' => 'English',
                        'de' => 'Dutch',
                    ])
                    ->suffixIcon('heroicon-m-language')
                    ->required(),

                Select::make('invoice_theme_id')
                    ->options(InvoiceThemeConstants::getFormattedThemes())
                    ->native(0)
                    ->required()
                    ->allowHtml()
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
                    ->suffixIcon('heroicon-m-currency-dollar')
                    ->label(__('Currency')),
                Actions::make([
                    Action::make('preview')
                        ->label(__('Generate Preview'))
                        ->icon('heroicon-o-eye')
                        ->color('gray')
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
        TenancyHelpers::getTenant()->update($data);
        Notification::make()->title(__('settings.profile-settings.notification'))->success()->send();
    }

    public function saveInvoiceSettingsForm(): void
    {
        $data = $this->toKeyValueArray($this->invoiceSettingsForm->getState());
        TenancyHelpers::getTenant()->settings()->update($data);
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
        return __('company_settings_page_heading');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['company', 'super_company']);
    }
}

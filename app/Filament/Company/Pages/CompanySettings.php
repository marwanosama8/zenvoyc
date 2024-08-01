<?php

namespace App\Filament\Company\Pages;

use App\Constants\InvoiceThemeConstants;
use App\Helpers\TenancyHelpers;
use App\Models\Company;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Tabs;

class CompanySettings extends Page
{

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.company.pages.company-settings';

    public function mount()
    {
        $companyProfileArray = TenancyHelpers::getTenant()->toArray();
        $companyInvoiceSettingArray = TenancyHelpers::getTenant()->settings()->first()->toArray();
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
                                    ->imageCropAspectRatio('92:16')
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
                                TextInput::make('address')->label('Address')->nullable(),
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
                TextInput::make('vat_percent')->numeric()->minValue(1)->maxValue(100)->inputMode('decimal')->label(__('vat_percent'))->hint(__('Loream ismapnn aqwouzpo pdocvb'))->nullable(),
                TextInput::make('invoice_language')->label(__('invoice_language'))
                    ->datalist([
                        'en_US',
                        'de_DE',
                    ])
                    ->nullable(),
                Select::make('invoice_theme')
                    ->options(InvoiceThemeConstants::getFormattedThemes())
                    ->native(0)
                    ->allowHtml()
                    ->label(__('invoice_theme'))->nullable(),

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

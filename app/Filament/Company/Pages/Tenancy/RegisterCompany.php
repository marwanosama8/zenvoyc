<?php

namespace App\Filament\Company\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\FileUpload;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('Register Comapny');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('General Information')
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
                    Wizard\Step::make('Address')
                        ->schema([
                            TextInput::make('address')->label('Address')->nullable(),
                            TextInput::make('postal_code')->label('Postal Code')->nullable(),
                        ]),
                    Wizard\Step::make('Back Information')
                        ->schema([
                            TextInput::make('tax_id')->label('Tax ID')->nullable(),
                            TextInput::make('vat_id')->label('VAT ID')->nullable(),
                            TextInput::make('iban')->label('IBAN')->nullable(),
                            TextInput::make('account_number')->label('Account Number')->nullable(),
                            TextInput::make('bank_code')->label('Bank Code')->nullable(),
                            TextInput::make('bic')->label('BIC')->nullable(),
                        ]),
                    Wizard\Step::make('Contact Information')
                        ->schema([
                            TextInput::make('contact_number')->label('Contact Number')->nullable(),
                            TextInput::make('contact_email')->label('Contact Email')->nullable(),
                        ]),
                ])

            ]);
    }

    protected function handleRegistration(array $data): Company
    {
        $data['slug'] =  Str::slug($data['name']) . '@' . random_int(1, 999);
        $comapny = Company::create($data);

        $comapny->users()->attach(auth()->user());

        return $comapny;
    }


}

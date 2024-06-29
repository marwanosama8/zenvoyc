<?php

namespace App\Filament\Company\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\Redirect;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('Comapny profile');
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
                                ->label('Avatar URL')->required(),
                            TextInput::make('name')->label('Company Name')->required(),
                            TextInput::make('managing_director')->label('Managing Director')->nullable(),
                            TextInput::make('legal_name')->label('Legal Name')->nullable(),
                            TextInput::make('website_url')->label('Website URL')->nullable(),
                            TextInput::make('place_of_jurisdiction')->label('Place of Jurisdiction')->nullable(),
                        ]),
                    Wizard\Step::make('Address')
                        ->schema([
                            RichEditor::make('address')
                            ->disableToolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'heading',
                                'italic',
                                'link',
                                'orderedList',
                                'strike',
                                'table',
                            ])->label('Address')->nullable(),
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
                ])->skippable()

            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Create new company')
                ->color('success')
                ->hidden(auth()->user()->hasRole('super_company'))
                ->action(fn () => redirect(env('APP_URL') . '/#plans'))
                ->requiresConfirmation()
                ->modalHeading('You need to subscribe to multi comapnies')
                ->modalDescription('you need to change your plan to Multi Comapies')
                ->modalSubmitActionLabel('Yes')
        ];
    }
}

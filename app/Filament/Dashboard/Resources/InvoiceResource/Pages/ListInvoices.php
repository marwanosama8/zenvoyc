<?php

namespace App\Filament\Dashboard\Resources\InvoiceResource\Pages;

use App\Filament\Dashboard\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        $isUserReadyToenerateInvoices = auth()->user()->userSetting->ready_to_generate;
        
        return [
            Actions\CreateAction::make()->disabled(!$isUserReadyToenerateInvoices),
            Actions\Action::make(__('auto_invoices'))
                ->disabled(!$isUserReadyToenerateInvoices)
                ->action(fn () => redirect(Filament::getTenant()->slug . "/auto-invoices")),
            Actions\Action::make('Invoice Setting')
                ->action(function (array $data): void {
                    auth()->user()->userSetting()->update($data);
                })
                ->fillForm(function (): array {
                    return auth()->user()->userSetting->toArray();
                })
                ->slideOver()
                ->steps([
                    Step::make('General Information')
                        ->schema([
                            Toggle::make('ready_to_generate')
                                ->label('Start generating')
                                ->hint('you must trigger this button then fill the below form to start generating invoices')
                                ->required()
                                ->live(),
                            FileUpload::make('avatar_url')
                                ->image()
                                ->imageEditor()
                                // ->imageCropAspectRatio('92:16')
                                // ->imageEditorAspectRatios([
                                //     '92:16'
                                // ])
                                ->label('Avatar URL')
                                ->required(),
                            TextInput::make('name')
                                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                                ->label('Company Name')->required(),
                            TextInput::make('managing_director')
                                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                                ->label('Managing Director')->nullable(),
                            TextInput::make('legal_name')
                                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                                ->label('Legal Name')->nullable(),
                            TextInput::make('website_url')
                                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                                ->label('Website URL')->nullable(),
                            TextInput::make('place_of_jurisdiction')
                                ->disabled(fn (Get $get): bool => !$get('ready_to_generate'))
                                ->label('Place of Jurisdiction')->nullable(),
                        ]),
                    Step::make('Address')
                        ->schema([
                            RichEditor::make('address')->label('Address')->nullable(),
                            TextInput::make('postal_code')->label('Postal Code')->nullable(),
                        ]),
                    Step::make('Back Information')
                        ->schema([
                            TextInput::make('tax_id')->label('Tax ID')->nullable(),
                            TextInput::make('vat_id')->label('VAT ID')->nullable(),
                            TextInput::make('iban')->label('IBAN')->nullable(),
                            TextInput::make('account_number')->label('Account Number')->nullable(),
                            TextInput::make('bank_code')->label('Bank Code')->nullable(),
                            TextInput::make('bic')->label('BIC')->nullable(),
                        ]),
                    Step::make('Contact Information')
                        ->schema([
                            TextInput::make('contact_number')->label('Contact Number')->nullable(),
                            TextInput::make('contact_email')->label('Contact Email')->nullable(),
                        ]),
                ])
                ->skippableSteps($isUserReadyToenerateInvoices)
                
        ];
    }
}

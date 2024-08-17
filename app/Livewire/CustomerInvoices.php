<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Models\TenantInvoice as Invoice;
use App\Models\Scopes\CustomerScope;
use App\Models\Scopes\TenantInvoiceScope as InvoiceScope;
use Carbon\Carbon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Livewire\Component as Livewire;

class CustomerInvoices extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $customer;
    public $lastThreeYears;

    public $selectedYear;

    public function mount($token)
    {
        $customer = Customer::withoutGlobalScope(CustomerScope::class)->whereToken($token)->first();
        abort_if(is_null($customer) || !$customer?->general_access, 404);
        $this->customer = $customer;
        $year = Carbon::now()->year;
        $this->lastThreeYears = range($year - 3, $year);
        $this->selectedYear = end($this->lastThreeYears);
    }

    public function table(Table $table): Table
    {

        return $table
            ->query(
                Invoice::query()->withoutGlobalScope(InvoiceScope::class)->where('customer_id', $this->customer->id)->whereYear('date_origin', $this->selectedYear)
            )
            ->columns([
                Tables\Columns\TextColumn::make('rgnr')
                    ->label(__("invoice.field.rgnr"))->getStateUsing(fn ($record) => $record->rgnr)
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_value')
                    ->label(__("invoice.field.invoice_value"))->getStateUsing(fn ($record) => number_format($record->getTotalNetto(), 2, ',', '.')),
                Tables\Columns\TextColumn::make('date_origin')
                    ->label(__("invoice.field.date_origin"))->date('d.m.Y'),
                Tables\Columns\TextColumn::make('date_pay')
                    ->label(__("invoice.field.date_pay"))->date('d.m.Y')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('viewInvoice')
                        ->label(__('invoice.action.view_invoice'))
                        ->icon('heroicon-m-eye')
                        ->openUrlInNewTab()
                        ->url(fn(Invoice $record) => route('invoice.view', $record->rgnr)),
                    Tables\Actions\Action::make('streamInvoice')
                        ->label(__('invoice.action.steam'))
                        ->openUrlInNewTab()
                        ->icon('heroicon-m-printer')
                        ->url(fn(Invoice $record) => route('invoice.stream', $record->rgnr)),
                    Action::make('generateXml')
                        ->label(__('invoice.action.generate_xml'))
                        ->icon('heroicon-m-code-bracket')
                        ->form([
                            Select::make('invoiceProfile')
                                ->label(__('invoice.action.invoiceProfile'))
                                ->options(config('zugferd-profiles.profiles'))
                                ->default(10)
                                ->required(),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->extraModalFooterActions([
                            Action::make('mergeWithPdf')
                                ->url(fn(Livewire $livewire, Invoice $record) => route('invoice.merge', ['rgnr' => $record->rgnr, 'profile' => $livewire->mountedTableActionsData[0]['invoiceProfile']]))
                                ->label(__('invoice.action.merge_with_pdf')),
                            Action::make('downloadXml')
                                ->url(fn(Livewire $livewire, Invoice $record) => route('invoice.ddxml', ['rgnr' => $record->rgnr, 'profile' => $livewire->mountedTableActionsData[0]['invoiceProfile']]))
                                ->label(__('invoice.action.xml_downlaod')),
                        ])
                ])
            ]);
    }



    public function changeYear($year)
    {
        $this->selectedYear = $year;
        $this->resetTable();
    }


    public function render()
    {
        return view('livewire.customer-invoices');
    }
}

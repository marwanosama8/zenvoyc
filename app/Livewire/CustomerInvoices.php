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
use App\Models\Invoice;
use App\Models\Scopes\CustomerScope;
use App\Models\Scopes\InvoiceScope;
use Carbon\Carbon;
use Filament\Tables;

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
        
        abort_if(is_null($customer), 404);
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
                Action::make('download')
                    ->label(__('invoice.link.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Invoice $record): string => route('invoice.download', ['invoice' => $record->invoice_number]))
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

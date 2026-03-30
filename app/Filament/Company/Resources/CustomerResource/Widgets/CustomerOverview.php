<?php

namespace App\Filament\Company\Resources\CustomerResource\Widgets;

use App\Filament\Company\Resources\InvoiceResource\Pages\ListInvoices;
use App\Helpers\CalculationHelpers;
use App\Helpers\TenancyHelpers;
use App\Models\TenantInvoice as Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Model;

class CustomerOverview extends BaseWidget
{
    public ?Model $record = null;


    protected function getStats(): array
    {

        $invoices = new Invoice();

        return [
            Stat::make(__('customer.widgets.year_invoices'), $invoices->where('customer_id', $this->record->id)->whereYear('date_origin', now()->year)->count())
                ->url(ListInvoices::getUrl(['tableFilters[customer_id][value]=' . $this->record->id])),
            Stat::make(__('customer.widgets.lifetime_invoices'), $invoices->where('customer_id', $this->record->id)->get()->count())
                ->url(ListInvoices::getUrl(['tableFilters[customer_id][value]=' . $this->record->id])),
            Stat::make(__('customer.widgets.year_invoice_cost'), $this->getYearInvoiceCost())
                ->url(ListInvoices::getUrl(['tableFilters[customer_id][value]=' . $this->record->id])),
            Stat::make(__('customer.widgets.lifetime_invoice_cost'), $this->getLifetimeInvoiceCost())
                ->url(ListInvoices::getUrl(['tableFilters[customer_id][value]=' . $this->record->id])),
        ];
    }

    private function getYearInvoiceCost()
    {
        $inovicedNotPayed = Invoice::whereYear('date_origin', now()->year)->where('customer_id', $this->record->id)->get();

        $payableArray = [];

        foreach ($inovicedNotPayed as  $invoice) {
            if ($invoice->has_vat) {
                $brutto = CalculationHelpers::getTotalBrutto($invoice->getTotalNetto(), $invoice->vat_percent);
            } else {
                $brutto = $invoice->getTotalNetto();
            }
            $payableArray[] = $brutto;
        }
        return TenancyHelpers::getCurrentModel()->settings->currency->symbol . Number::forHumans(array_sum($payableArray), maxPrecision: 2, abbreviate: true);
    }

    private function getLifetimeInvoiceCost()
    {
        $inovicedNotPayed = Invoice::where('customer_id', $this->record->id)->get();

        $payableArray = [];

        foreach ($inovicedNotPayed as  $invoice) {
            if ($invoice->has_vat) {
                $brutto = CalculationHelpers::getTotalBrutto($invoice->getTotalNetto(), $invoice->vat_percent);
            } else {
                $brutto = $invoice->getTotalNetto();
            }
            $payableArray[] = $brutto;
        }
        return TenancyHelpers::getCurrentModel()->settings->currency->symbol . Number::forHumans(array_sum($payableArray), maxPrecision: 2, abbreviate: true);
    }
}

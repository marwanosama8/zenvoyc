<?php

namespace App\Filament\User\Widgets\Dashboard;

use App\Filament\User\Resources\InvoiceResource\Pages\ListInvoices;
use App\Helpers\CalculationHelpers;
use App\Helpers\TenancyHelpers;
use App\Models\TenantInvoice;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget;


class InvoicesStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        return [    
            Stat::make(__('stats.open_cash'), $this->getInvoicedButNotPayed())
                ->url(ListInvoices::getUrl()),
            Stat::make(__('stats.taxes'), $this->getTotalVat())
                ->url(ListInvoices::getUrl(['tableFilters[has_vat][isActive]=true'])),
            // Stat::make(__('state.cashburn'),$this->getCashburn()),
            Stat::make(__('state.revenue'), $this->getRevenue($startDate, $endDate))
                ->url(ListInvoices::getUrl()),

        ];
    }

    private function getInvoicedButNotPayed()
    {
        $inovicedNotPayed = TenantInvoice::wherePayed(false)->get();

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

    private function getTotalVat()
    {
        $vatTotal = TenantInvoice::whereHasVat(true)->get();

        $totalVatArray = [];

        foreach ($vatTotal as $invoice) {
            $vatPrice = CalculationHelpers::getTotalVat($invoice->getTotalNetto(), $invoice->vat_percent);
            $totalVatArray[] = $vatPrice;
        }

        return TenancyHelpers::getCurrentModel()->settings->currency->symbol . Number::forHumans(array_sum($totalVatArray));
    }

    // public function getCashburn()
    // {

    // }


    public function getRevenue($startDate, $endDate)
    {
        // dd($startDate);
        $allInvoices = TenantInvoice::query()
            ->when($startDate, fn(Builder $query) => $query->whereDate('date_origin', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('date_origin', '<=', $endDate))
            ->get();

        $payableArray = [];

        foreach ($allInvoices as  $invoice) {
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

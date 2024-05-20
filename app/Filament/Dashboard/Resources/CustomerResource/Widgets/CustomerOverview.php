<?php

namespace App\Filament\Dashboard\Resources\CustomerResource\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $invoices = new Invoice();

        return [
            Stat::make(__('customer.widgets.year_invoices'), $invoices->whereYear('date_origin',now()->year)->count()),
            Stat::make(__('customer.widgets.lifetime_invoices'), $invoices->all()->count()),
        ];
    }
}

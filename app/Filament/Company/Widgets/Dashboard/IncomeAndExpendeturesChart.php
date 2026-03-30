<?php

namespace App\Filament\Company\Widgets\Dashboard;

use App\Models\Expenditure;
use App\Models\TenantInvoice;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Contracts\Support\Htmlable;

class IncomeAndExpendeturesChart extends ChartWidget
{


    protected function getData(): array
    {
        $exp = Trend::model(Expenditure::class)
            ->between(
                start: now()->year(2025)->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();
        $invoicePayed = Trend::query(
            TenantInvoice::query()
                ->hasBeenPaid()
        )
            ->dateColumn('date_origin')
            ->between(
                start: now()->year(2025)->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();
        $invoiceUnpayed = Trend::query(
            TenantInvoice::query()
                ->hasNotPayed()
        )
            ->dateColumn('date_origin')
            ->between(
                start: now()->year(2025)->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('widgets.expenditures'),
                    'backgroundColor' => 'red',
                    'borderColor' => 'red',
                    'data' => $exp->map(fn(TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => __('widgets.payey_invocices'),
                    'backgroundColor' => 'blue',
                    'borderColor' => 'blue',
                    'data' => $invoicePayed->map(fn(TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => __('widgets.unpayey_invocices'),
                    'backgroundColor' => 'green',
                    'borderColor' => 'green',
                    'data' => $invoiceUnpayed->map(fn(TrendValue $value) => $value->aggregate),
                ]
            ],
            'labels' => $exp->map(fn(TrendValue $value) => $value->date),
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
    public function getHeading(): string|Htmlable|null
    {
        return __('widget.income_vs_expendituries');
    }
}

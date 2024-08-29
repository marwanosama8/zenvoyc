<?php

namespace App\Filament\Dashboard\Widgets\Dashboard;

use App\Models\Expenditure;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class ExpenditureWidget extends Widget
{
    use InteractsWithPageFilters;

    public $selectedFrequency = ['monthly', 'one-time', 'yearly'];

    protected static ?int $sort = 3;

    public function mount() {}

    public function render(): View
    {
        $expenditures = Expenditure::query();

        if (count($this->selectedFrequency) < 3) {
            $expenditures->whereIn('frequency', $this->selectedFrequency);
        }

        $expenditures = $expenditures->get();
        $results = [];

        foreach ($expenditures as $expenditure) {
            $startTime = $this->getStartDate($expenditure->start);
            $endTime = $this->getEndDate($expenditure->end);

            switch ($expenditure->frequency->value) {
                case 'one-time':
                    $results[] = $expenditure->cost;
                    break;

                case 'monthly':
                    $monthsDifference = $startTime->diffInMonths($endTime, true);
                    $results[] = $expenditure->cost * round($monthsDifference);
                    break;

                case 'yearly':
                    $yearsDifference = $startTime->diffInYears($endTime, true);
                    $results[] = $expenditure->cost * round($yearsDifference);
                    break;
            }
        }
        $totalCost = array_sum($results);
        return view('filament.dashboard.widgets.dashboard.expenditure-widget', ['totalCost' => $totalCost]);
    }

    private function getStartDate($exp_startDate)
    {
        $filterdStartDate = Carbon::parse($this->filters['startDate'] ?? now()->firstOfYear()->toDateString());
        if ($exp_startDate->lt($filterdStartDate)) {
            return $filterdStartDate;
        }
        return $exp_startDate;
    }

    private function getEndDate($exp_endDate)
    {
        $filterdEndDate = Carbon::parse($this->filters['endDate'] ?? now()->toDateString());
        if ($filterdEndDate->gt($exp_endDate)) {
            return $exp_endDate;
        }
        return $filterdEndDate;
    }
}

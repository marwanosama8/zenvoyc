<?php

namespace App\Filament\Dashboard\Resources\ExpenditureResource\Pages;

use App\Filament\Dashboard\Resources\ExpenditureResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenditure extends EditRecord
{
    protected static string $resource = ExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!array_key_exists('end',$data)) {
            $data['end'] = now();
        }
        return $data;
    }

    public function getStartDateOption($frequency)
    {
        switch ($frequency) {
            case 'one-time':
                return  [Carbon::today()->toDateString()];
            case 'monthly':
                return $this->getMonthlyOption();
            case 'yearly':
                return $this->getYearlyOption();
        }
    }


    private function getMonthlyOption(): array
    {
        $currentYear = Carbon::now()->year;
        $currentDay = Carbon::now()->day;
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $key = Carbon::create($currentYear, $i, $currentDay)->format('Y-m-d');
            $months[$key] = Carbon::createFromFormat('m', $i)->format('F');
        }
        $months;
        return $months;
    }
    private function getYearlyOption(): array
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;
        $years = [];

        for ($i = 0; $i < 10; $i++) {
            $year = $currentYear + $i;
            $key = Carbon::create($year, $currentMonth, $currentDay)->format('Y-m-d');
            $years[$key] = $year;
        }

        return $years;
    }


    public function getEndDateOption($frequency, $startDate = null)
    {
        switch ($frequency) {
            case 'one-yime':
                return  [Carbon::today()->toDateString()];
            case 'monthly':
                return $this->getMonthlyEndDateOption($startDate);
            case 'yearly':
                return $this->getYearlyEndDateOption($startDate);
        }
    }

    private function getMonthlyEndDateOption($startDate): array
    {
        $start = Carbon::create( $startDate);
        $currentYear = $start->year;
        $currentMonth = $start->month;
        $currentDay = $start->day;

        $months = [];
        for ($i = $currentMonth + 1; $i <= 12; $i++) {
            $key = Carbon::create($currentYear, $i, $currentDay)->format('Y-m-d');
            $months[$key] = Carbon::createFromFormat('m', $i)->format('F');
        }
        return $months;
    }

    private function getYearlyEndDateOption($startDate): array
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $currentYear = $start->year;
        $currentMonth = $start->month;
        $currentDay = $start->day;

        $years = [];
        for ($i = 1; $i <= 10; $i++) {
            $year = $currentYear + $i;
            $key = Carbon::create($year, $currentMonth, $currentDay)->format('Y-m-d');
            $years[$key] = $year;
        }

        return $years;
    }
}

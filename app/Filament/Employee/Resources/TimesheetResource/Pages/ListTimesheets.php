<?php

namespace App\Filament\Employee\Resources\TimesheetResource\Pages;

use App\Filament\Employee\Resources\TimesheetResource;
use App\Filament\Dashboard\Widgets\TimesheetTracker;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    // public static function getWidgets(): array
    // {
    //     return [
    //         TimesheetOverview::class,
    //     ];
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            TimesheetTracker::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('timesheet.create_manual_timesheet')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'this_week' => Tab::make(__('filament-timesheet::timesheet.this_week'))->query(fn ($query) => $query->thisWeek()),
            'last_week' => Tab::make(__('filament-timesheet::timesheet.last_week'))->query(fn ($query) => $query->lastWeek()),
            'last_month' => Tab::make(__('filament-timesheet::timesheet.last_month'))->query(fn ($query) => $query->lastMonth()),
            'last_quarter' => Tab::make(__('filament-timesheet::timesheet.last_quarter'))->query(fn ($query) => $query->lastQuarter()),
            'this_year' => Tab::make(__('filament-timesheet::timesheet.this_year'))->query(fn ($query) => $query->thisYear()),
            'all' => Tab::make(__('filament-timesheet::timesheet.all')),
        ];
    }
}
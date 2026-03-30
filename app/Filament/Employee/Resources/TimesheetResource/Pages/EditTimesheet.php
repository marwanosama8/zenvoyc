<?php

namespace App\Filament\Employee\Resources\TimesheetResource\Pages;

use App\Filament\Employee\Resources\TimesheetResource;
use App\Helpers\TenancyHelpers;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('manual_time', $data)) {
            $end_time = \App\Helpers\CalculationHelpers::getEndTimeAfterParseToTimeString($data['start_time'], $data['manual_time']);
            $data['end_time'] = $end_time;
        }
        return $data;
    }
}

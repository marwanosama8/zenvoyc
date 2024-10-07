<?php

namespace App\Filament\User\Resources\TimesheetResource\Pages;

use App\Filament\User\Resources\TimesheetResource;
use App\Helpers\TenancyHelpers;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (array_key_exists('manual_time', $data)) {
            $end_time = \App\Helpers\CalculationHelpers::getEndTimeAfterParseToTimeString($data['start_time'],$data['manual_time']) ;
            $data['end_time'] = $end_time;
        }
        return $data;
    }
}

<?php

namespace App\Filament\Company\Resources\TimesheetResource\Pages;

use App\Filament\Company\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;
}

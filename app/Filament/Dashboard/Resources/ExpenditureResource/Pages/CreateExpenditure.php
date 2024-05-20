<?php

namespace App\Filament\Dashboard\Resources\ExpenditureResource\Pages;

use App\Filament\Dashboard\Resources\ExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenditure extends CreateRecord
{
    protected static string $resource = ExpenditureResource::class;
}

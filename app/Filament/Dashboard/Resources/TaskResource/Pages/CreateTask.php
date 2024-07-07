<?php

namespace App\Filament\Dashboard\Resources\TaskResource\Pages;

use App\Filament\Dashboard\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}

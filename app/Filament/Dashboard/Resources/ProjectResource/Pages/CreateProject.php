<?php

namespace App\Filament\Dashboard\Resources\ProjectResource\Pages;

use App\Filament\Dashboard\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
}

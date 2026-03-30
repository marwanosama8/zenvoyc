<?php

namespace App\Filament\Company\Resources\ContactResource\Pages;

use App\Filament\Company\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}

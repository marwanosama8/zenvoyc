<?php

namespace App\Filament\Dashboard\Resources\CustomerResource\Pages;

use App\Filament\Dashboard\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}

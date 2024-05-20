<?php

namespace App\Filament\Company\Resources\CustomerResource\Pages;

use App\Filament\Company\Resources\CustomerResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

}

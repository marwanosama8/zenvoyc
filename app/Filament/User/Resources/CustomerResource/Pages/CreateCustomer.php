<?php

namespace App\Filament\User\Resources\CustomerResource\Pages;

use App\Filament\User\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}

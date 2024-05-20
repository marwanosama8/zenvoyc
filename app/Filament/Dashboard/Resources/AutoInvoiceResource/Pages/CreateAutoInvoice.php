<?php

namespace App\Filament\Dashboard\Resources\AutoInvoiceResource\Pages;

use App\Filament\Dashboard\Resources\AutoInvoiceResource;
use App\Models\AutoInvoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAutoInvoice extends CreateRecord
{
    protected static string $resource = AutoInvoiceResource::class;
}

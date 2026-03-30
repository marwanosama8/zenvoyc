<?php

namespace App\Filament\Company\Resources\AutoInvoiceResource\Pages;

use App\Filament\Company\Resources\AutoInvoiceResource;
use App\Models\AutoInvoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAutoInvoice extends CreateRecord
{
    protected static string $resource = AutoInvoiceResource::class;
}

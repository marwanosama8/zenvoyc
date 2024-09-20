<?php

namespace App\Filament\Dashboard\Resources\InvoiceResource\Pages;

use App\Filament\Dashboard\Resources\InvoiceResource;
use App\Helpers\TenancyHelpers;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['vat_percent'] = TenancyHelpers::getVatPercent();
     
        return $data;
    }

}

<?php

namespace App\Filament\User\Resources\InvoiceResource\Pages;

use App\Filament\User\Resources\InvoiceResource;
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

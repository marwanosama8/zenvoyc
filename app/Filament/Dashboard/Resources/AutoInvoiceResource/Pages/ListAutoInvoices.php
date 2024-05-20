<?php

namespace App\Filament\Dashboard\Resources\AutoInvoiceResource\Pages;

use App\Filament\Dashboard\Resources\AutoInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutoInvoices extends ListRecords
{
    protected static string $resource = AutoInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

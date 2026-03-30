<?php

namespace App\Filament\User\Resources\AutoInvoiceResource\Pages;

use App\Filament\User\Resources\AutoInvoiceResource;
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

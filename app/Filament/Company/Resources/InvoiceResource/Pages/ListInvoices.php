<?php

namespace App\Filament\Company\Resources\InvoiceResource\Pages;

use App\Filament\Company\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make(__('auto_invoices'))
            ->action(fn () => redirect(Filament::getTenant()->slug."/auto-invoices")),
        ];
    }
}

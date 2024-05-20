<?php

namespace App\Filament\Dashboard\Resources\AutoInvoiceResource\Pages;

use App\Filament\Dashboard\Resources\AutoInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoInvoice extends EditRecord
{
    protected static string $resource = AutoInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Dashboard\Resources\SalesResource\Pages;

use App\Filament\Dashboard\Resources\SalesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSales extends EditRecord
{
    protected static string $resource = SalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

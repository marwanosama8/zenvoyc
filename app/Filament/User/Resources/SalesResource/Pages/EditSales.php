<?php

namespace App\Filament\User\Resources\SalesResource\Pages;

use App\Filament\User\Resources\SalesResource;
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

<?php

namespace App\Filament\Dashboard\Resources\ExpenditureResource\Pages;

use App\Filament\Dashboard\Resources\ExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenditures extends ListRecords
{
    protected static string $resource = ExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

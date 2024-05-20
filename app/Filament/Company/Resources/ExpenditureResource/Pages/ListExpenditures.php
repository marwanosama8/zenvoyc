<?php

namespace App\Filament\Company\Resources\ExpenditureResource\Pages;

use App\Filament\Company\Resources\ExpenditureResource;
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

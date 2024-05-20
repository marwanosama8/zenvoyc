<?php

namespace App\Filament\Dashboard\Resources\ExpenditureResource\Pages;

use App\Filament\Dashboard\Resources\ExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenditure extends EditRecord
{
    protected static string $resource = ExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}

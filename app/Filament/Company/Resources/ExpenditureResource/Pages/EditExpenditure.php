<?php

namespace App\Filament\Company\Resources\ExpenditureResource\Pages;

use App\Filament\Company\Resources\ExpenditureResource;
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

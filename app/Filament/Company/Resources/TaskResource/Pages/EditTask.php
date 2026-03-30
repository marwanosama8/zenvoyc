<?php

namespace App\Filament\Company\Resources\TaskResource\Pages;

use App\Filament\Company\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Parallax\FilamentComments\Actions\CommentsAction;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

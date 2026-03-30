<?php

namespace App\Filament\Company\Resources\TaskResource\Pages;

use App\Filament\Company\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Parallax\FilamentComments\Actions\CommentsAction;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
        ];
    }
}

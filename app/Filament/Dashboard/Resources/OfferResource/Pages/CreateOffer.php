<?php

namespace App\Filament\Dashboard\Resources\OfferResource\Pages;

use App\Filament\Dashboard\Resources\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Str;

class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] =  Str::random(40);
        
        return $data;
    }
}

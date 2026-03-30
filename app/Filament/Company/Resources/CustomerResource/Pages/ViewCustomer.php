<?php

namespace App\Filament\Company\Resources\CustomerResource\Pages;

use App\Filament\Company\Resources\CustomerResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;
    
    protected function getHeaderWidgets(): array
    {
        return [
            CustomerResource\Widgets\CustomerOverview::class,
        ];
    }
    protected function getFooterWidgets(): array
    {
        return [
            // 
        ];
    }
}

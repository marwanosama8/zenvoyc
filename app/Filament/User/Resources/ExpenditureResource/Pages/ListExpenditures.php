<?php

namespace App\Filament\User\Resources\ExpenditureResource\Pages;

use App\Enums\FrequencyEnums;
use App\Filament\User\Resources\ExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListExpenditures extends ListRecords
{
    protected static string $resource = ExpenditureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'One-time' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('frequency', FrequencyEnums::OneTime)),
            'Monthly' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('frequency', FrequencyEnums::Monthly)),
            'Yearly' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('frequency', FrequencyEnums::Yearly)),
        ];
    }
}

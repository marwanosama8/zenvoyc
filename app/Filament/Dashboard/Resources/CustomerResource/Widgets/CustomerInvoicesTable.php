<?php

namespace App\Filament\Dashboard\Resources\CustomerResource\Widgets;

use App\Filament\Dashboard\Resources\InvoiceResource;
use App\Models\TenantInvoice as Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoicesTable extends BaseWidget
{
    public ?Model $record = null;
    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
               InvoiceResource::getEloquentQuery()->where('customer_id',$this->record->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('rgnr')
                    ->label(__("invoice.field.rgnr"))->getStateUsing(fn ($record) => $record->rgnr)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__("invoice.field.customer"))->getStateUsing(fn ($record) => $record->customer->name)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_value')
                    ->label(__("invoice.field.invoice_value"))->getStateUsing(fn ($record) => number_format($record->getTotalNetto(), 2, ',', '.')),
                Tables\Columns\TextColumn::make('date_origin')
                    ->label(__("invoice.field.date_origin"))->date('d.m.Y'),
                Tables\Columns\TextColumn::make('date_pay')
                    ->label(__("invoice.field.date_pay"))->date('d.m.Y'),
                Tables\Columns\ToggleColumn::make('payed')
                    ->label(__("invoice.field.payed")),
                ]);
    }
}

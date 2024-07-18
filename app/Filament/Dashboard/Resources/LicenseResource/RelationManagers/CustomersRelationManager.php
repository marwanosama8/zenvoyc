<?php

namespace App\Filament\Dashboard\Resources\LicenseResource\RelationManagers;

use App\Helpers\TenancyHelpers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\AttachAction;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                // ->relationship('customer', 'name')
                ->options(TenancyHelpers::getPluckCustomers())
                ->live()
                ->required()
                // ->afterStateUpdated(function (Set $set, ?string $state) {
                //     session(['current_customer_id' => $state]);
                // })
                ->native(false)
                ->label(__("invoice.field.customer"))
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('volume'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                ->form(fn (AttachAction $action): array => [
                    // $action->getRecordSelect(),
                    Forms\Components\Select::make('recordId')
                    ->options(TenancyHelpers::getPluckCustomers())
                    ->required()
                    ->native(false)
                    ->label(__("invoice.field.customer")),
                    Forms\Components\TextInput::make('volume')->required()
                        ->numeric()
                        ->maxLength(255),
                ])

                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),

                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make(),

                ]),
            ]);
    }
}

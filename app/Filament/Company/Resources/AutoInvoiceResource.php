<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\AutoInvoiceResource\Pages;
use App\Filament\Company\Resources\AutoInvoiceResource\RelationManagers;
use App\Helpers\CalculationHelpers;
use App\Helpers\TenancyHelpers;
use App\Models\AutoInvoice;
use App\Models\Customer;
use App\Models\Sales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AutoInvoiceResource extends Resource
{
    protected static ?string $model = AutoInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->required()
                    ->native(0)
                    ->searchable()
                    ->options(TenancyHelpers::getPluckCustomers())
                    ->label(__("invoice.field.customer")),
                Forms\Components\TextInput::make('rgnr')
                    ->required()
                    ->maxLength(50)
                    ->label(__("invoice.field.rgnr")),
                Forms\Components\Textarea::make('customer_address')
                ->required()
                    ->label(__("invoice.field.customer_address"))
                    ->maxLength(65535),
                Forms\Components\Textarea::make('options')
                    ->label(__("invoice.field.options")),
                Forms\Components\Textarea::make('info')
                    ->label(__("invoice.field.info"))
                    ->maxLength(65535),
                Forms\Components\TextInput::make('rate')
                    ->label(__("invoice.field.rate"))
                    ->numeric(),
                Forms\Components\Repeater::make('items')
                    ->label(__("invoice.field.items"))
                    ->orderColumn('order_column')
                    ->cloneable()
                    ->addActionLabel(__("invoice.field.add_item"))
                    ->schema([
                        Forms\Components\Select::make('product')
                            ->hint(__('invoice.hint.product'))
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $product =  Sales::find($state);

                                $set('type', 2);
                                $set('amount', $product->price);
                                $set('description', $product->description);
                            })
                            ->native(false)
                            ->searchable()
                            ->hiddenOn('edit')

                            ->options(TenancyHelpers::getPluckSales()),
                        Forms\Components\Textarea::make('description')
                            ->label(__("invoice.field.description"))->rows(5)->required(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label(__("invoice.field.type"))
                                    ->required()
                                    ->options([
                                        '1' => __("invoice.select.type1"),
                                        '2' => __("invoice.select.type2"),
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $product =  Sales::find($get('product'));
                                        if (!$product) {
                                            return;
                                        }
                                        if ($state == 2) {
                                            $set('amount', $product->price);
                                        } else {
                                            $customer = Customer::find(session()->get('current_customer_id'));

                                            $set('amount', number_format((float)$customer->rate * $product->price, 2, '.', ''));
                                        }
                                    }),
                                Forms\Components\TextInput::make('amount')
                                    ->label(__("invoice.field.amount"))->numeric()->required(),

                                Forms\Components\TextInput::make('price')
                                    ->label(__("invoice.field.price"))->numeric()->required(),
                            ]),

                    ])
                    ->columnSpanFull(),
                Forms\Components\Select::make('custom_interval')
                    ->label(__("invoice.field.custom_interval"))
                    ->options(config('auto_invoice.custom_interval'))
                    ->required(),
                Forms\Components\DatePicker::make('next_generate_date')
                    ->label(__("invoice.field.next_generate_date"))
                    ->default(now()->addMonth())
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rgnr')
                    ->label(__("invoice.field.rgnr"))->getStateUsing(fn ($record) => $record->rgnr)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__("invoice.field.customer"))->getStateUsing(fn ($record) => $record->customer->name)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('custom_interval')
                    ->alignCenter()
                    ->label(__("invoice.field.custom_interval"))
                    ->getStateUsing(fn ($record) => config('auto_invoice.custom_interval')[$record->custom_interval])
                    ->badge(),
                Tables\Columns\TextColumn::make('next_generate_date')
                    
                    ->label(__("invoice.field.next_generate_date"))->date('d.m.Y'),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutoInvoices::route('/'),
            'create' => Pages\CreateAutoInvoice::route('/create'),
            'edit' => Pages\EditAutoInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

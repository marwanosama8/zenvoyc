<?php

namespace App\Filament\Dashboard\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\TenantInvoice as Invoice;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Customer;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Dashboard\Resources\InvoiceResource\Pages;
use App\Helpers\TenancyHelpers;
use App\Models\InvoiceItem;
use App\Models\Sales;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
  
    public static function getModelLabel(): string
    {
        return __('navigation.invoice');
    }

  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.invoices');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.invoice');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.finance');
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->options(TenancyHelpers::getPluckCustomers())
                        ->live()
                        ->required()
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            session(['current_customer_id' => $state]);
                        })
                        ->native(false)
                        ->label(__("invoice.field.customer")),

                    Forms\Components\TextInput::make('rgnr')
                        ->label(__("invoice.field.rgnr"))
                        ->default(Invoice::getNextNr()),

                    Forms\Components\DatePicker::make('date_origin')
                        ->label(__("invoice.field.date_origin"))
                        ->default(Carbon::now()->format('Y-m-d')),
                    Forms\Components\DatePicker::make('date_pay')
                        ->label(__("invoice.field.date_pay"))
                        ->default(Carbon::now()->addDays(14)->format('Y-m-d')),

                    Forms\Components\DatePicker::make('date_start')
                        ->label(__("invoice.field.date_start"))
                        ->default(Carbon::now()->firstOfMonth()->format('Y-m-d')),

                    Forms\Components\DatePicker::make('date_end')
                        ->label(__("invoice.field.date_end"))
                        ->live()
                        ->default(Carbon::now()->lastOfMonth()->format('Y-m-d')),
                ]),
            Forms\Components\Repeater::make('invoice_item')
                ->relationship('invoice_item')
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
                                        return ;
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
                                ->label(__("invoice.field.price"))
                                ->numeric()
                                ->disabled(fn (Get $get): bool => !filled($get('type')))
                                ->minValue(1)
                                ->inputMode('decimal')
                                ->required(),
                        ]),
                ])
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    unset($data['product']);
                    return $data;
                })
                ->columns(1),

            Forms\Components\Textarea::make('info')
                ->label(__("invoice.field.information"))->rows(5),
        ])->columns(1);
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
                Tables\Columns\TextColumn::make('invoice_value')
                    ->label(__("invoice.field.invoice_value"))->getStateUsing(fn ($record) => number_format($record->getTotalNetto(), 2, ',', '.')),
                Tables\Columns\TextColumn::make('date_origin')
                    ->label(__("invoice.field.date_origin"))->date('d.m.Y'),
                Tables\Columns\TextColumn::make('date_pay')
                    ->label(__("invoice.field.date_pay"))->date('d.m.Y'),
                Tables\Columns\ToggleColumn::make('payed')
                    ->label(__("invoice.field.payed")),
                Tables\Columns\TextColumn::make('#')
                    ->getStateUsing(function ($record) {
                        return '<a target="_blank" href="' . route('invoice.view', ['invoice' => $record->rgnr]) . '">' . __('invoice.link.view') . '</a>';
                    })->html(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('not_send')
                    ->label(__('invoice.filter.not_sended'))
                    ->query(fn (Builder $query): Builder => $query->where('send', 0))
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('reminder_email')
                        ->label(__('invoice.action.reminder_email'))
                        ->icon('heroicon-m-chat-bubble-bottom-center-text')
                        ->url(fn (Invoice $record) => route('invoice.reminder', $record)),
                    Tables\Actions\Action::make('duplicate')
                        ->label(__('invoice.action.duplicate'))
                        ->icon('heroicon-m-document-duplicate')
                        ->url(fn (Invoice $record) => route('invoice.duplicate', $record)),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\InvoiceItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

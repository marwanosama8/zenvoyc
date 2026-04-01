<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\AutoInvoiceResource\Pages;
use App\Filament\Company\Resources\AutoInvoiceResource\RelationManagers;
use App\Helpers\CalculationHelpers;
use App\Helpers\TenancyHelpers;
use App\Models\AutoInvoice;
use App\Models\Customer;
use App\Models\Sales;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component as Livewire;
use function Aws\map;


class AutoInvoiceResource extends Resource
{
    protected static ?string $model = AutoInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getModelLabel(): string
    {
        return __('navigation.auto_invoice');
    }


    public static function getPluralModelLabel(): string
    {
        return __('navigation.auto_invoices');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.auto_invoice');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.finance');
    }
    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Section::make('customer')
                            ->columns(2)
                            ->label(__('invoice.customer_details'))
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->options(TenancyHelpers::getPluckCustomers())
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                                        if ($operation === 'create') {
                                            $customer = Customer::find($state);
                                            $set('rate', $customer->rate ?? 0);
                                            $set('customer_address', $customer->full_customer_address ?? 0);
                                        }
                                    })
                                    ->columnSpanFull()
                                    ->native(false)
                                    ->label(__("invoice.field.customer")),
                            ]),



                    ]),
                Section::make('invoice items')
                    ->label(__('invoice.invoice_items'))
                    ->disabled(fn (Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->orderColumn('order_column')
                            ->cloneable()
                            ->required()
                            ->addActionLabel(__("invoice.field.add_item"))
                            ->schema([
                                Forms\Components\Select::make('product')
                                    ->hint(__('invoice.hint.product'))
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $product =  Sales::find($state);

                                        $set('type', 2);
                                        $set('price', $get('amount') * $product?->price ?? 1);
                                        $set('unit_price', $product?->price ?? 1);
                                        $set('description', $product?->description ?? '');
                                    })
                                    ->native(true)
                                    ->searchable()
                                    ->lazy()
                                    ->hiddenOn('edit')
                                    ->options(TenancyHelpers::getPluckSales()),
                                Forms\Components\Textarea::make('description')
                                    ->label(__("invoice.field.description"))->rows(5)->required(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'lg' => 4,
                                        ])
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->label(__("invoice.field.type"))
                                                    ->required()
                                                    ->options([
                                                        '1' => __("invoice.select.type1"),
                                                        '2' => __("invoice.select.type2"),
                                                    ])
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, Livewire $livewire) {
                                                        switch ($state) {
                                                            case 1:
                                                                $set('unit_price', $livewire->data['rate']);
                                                                $set('price', number_format((float)$livewire->data['rate'] * $get('amount') ?? 0, 2, '.', ''));
                                                                break;

                                                            case 2:
                                                                $set('amount', 1 ?? 0);
                                                                $set('unit_price', 0);
                                                                $set('price', 0);
                                                                break;

                                                            default:
                                                                break;
                                                        }
                                                    }),
                                                Forms\Components\TextInput::make('amount')
                                                    ->label(__("invoice.field.amount"))
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->disabled(fn (Get $get): bool => !filled($get('type')))
                                                    ->inputMode('decimal')
                                                    ->numeric()
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, Livewire $livewire) {
                                                        $amount = filled($get('amount')) ? $get('amount') : 0.00;
                                                        switch ($get('type')) {
                                                            case 1:
                                                                $price =  $get('unit_price') ?? 0;
                                                                $set('price', number_format((float)$livewire->data['rate'] * $amount ?? 0.00, 2, '.', ''));
                                                                break;
                                                            case 2:
                                                                $price = $get('unit_price') ?? 0;
                                                                $set('price', $state * $price);
                                                                break;

                                                            default:
                                                                break;
                                                        }
                                                    })
                                                    ->required(),
                                                Forms\Components\TextInput::make('unit_price')
                                                    ->label(fn (Get $get): string => $get('type') == 1 ? __("invoice.field.per_hour") : __("invoice.field.unit_price"))
                                                    ->numeric()
                                                    ->hidden(fn ($operation): bool => $operation == 'edit')
                                                    ->minValue(1)
                                                    ->live()
                                                    ->readOnly(fn (Get $get): bool => $get('type') == 1)
                                                    ->disabled(fn (Get $get): bool => !filled($get('type')))
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, Livewire $livewire) {
                                                        $amount = filled($get('amount')) ? $get('amount') : 0.00;

                                                        switch ($get('type')) {
                                                            case 1:
                                                                $set('price', number_format((float)$livewire->data['rate'] * $amount ?? 0, 2, '.', ''));
                                                                break;

                                                            case 2:
                                                                $set('price', $state * $amount ?? 0);
                                                                break;

                                                            default:
                                                                break;
                                                        }
                                                    })
                                                    ->required(fn ($operation): bool => $operation == 'create'),
                                                Forms\Components\TextInput::make('price')
                                                    ->label(__("invoice.field.price"))
                                                    ->numeric()
                                                    ->disabled(fn (Get $get): bool => !filled($get('type')))
                                                    ->minValue(1)
                                                    ->inputMode('decimal')
                                                    ->required(),
                                            ]),
                                    ]),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                unset($data['product'], $data['unit_price']);
                                return $data;
                            })
                            ->columns(1),
                    ]),
                Split::make([
                    Section::make('info')
                        ->disabled(fn (Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                        ->label(__('invoice.info'))
                        ->description(__('invoice.here_you_can_define_invoice_footer'))
                        ->schema([
                            Forms\Components\Textarea::make('info')
                                ->label(__("invoice.field.information"))->rows(5)
                        ]),
                    Section::make('invoice options')
                        ->disabled(fn (Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                        ->label(__('invoice.invoice_options'))
                        ->description(__('invoice.here_you_can_define_invoice_options'))
                        ->schema([
                            Toggle::make('has_vat')
                                ->default(1)
                                ->label(__('invoice.has_vat'))
                        ])->grow(false),
                ])->from('md'),
                Section::make('Date Details')
                    ->label(__('invoice.date_details'))
                    ->columns(2)
                    ->disabled(fn (Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                    ->schema([
                        Forms\Components\Select::make('custom_interval')
                            ->label(__("invoice.field.custom_interval"))
                            ->options(config('auto_invoice.custom_interval'))
                            ->required(),
                        Forms\Components\DatePicker::make('next_generate_date')
                            ->label(__("invoice.field.next_generate_date"))
                            ->default(now()->addMonth())
                            ->required()
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__("invoice.field.customer"))->getStateUsing(fn ($record) => $record->customer->name)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('custom_interval')
                    ->alignCenter()
                    ->label(__("invoice.field.custom_interval"))
                    ->getStateUsing(fn ($record) => config('auto_invoice.custom_interval')[$record->custom_interval])
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->alignCenter()
                    ->label(__("invoice.field.price"))
                    ->getStateUsing(fn ($record) => '$'.collect($record->items)->sum('price'))
                    ,
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

<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\InvoiceResource\Api\Transformers\InvoiceTransformer;
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
use App\Filament\Company\Resources\InvoiceResource\Pages;
use App\Helpers\TenancyHelpers;
use App\Models\InvoiceItem;
use App\Models\Sales;
// use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Livewire\Component as Livewire;
use Filament\Forms\Components\Select;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

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
    protected static bool $isScopedToTenant = true;
    protected static ?string $tenantOwnershipRelationshipName = 'company';

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                                ->native(false)
                                ->label(__("invoice.field.customer")),

                            Forms\Components\TextInput::make('rate')
                                ->readOnly()
                                ->numeric()
                                ->live()
                                ->minValue(1.00)
                                ->inputMode('decimal')
                                ->default('0.00')
                                ->label(__("invoice.field.rate")),
                            Forms\Components\TextInput::make('customer_address')
                                ->columnSpanFull()
                                ->disabled(fn(Livewire $livewire, $operation): bool => is_null($livewire->data['customer_id']) || $operation == 'edit')
                                ->label(__("invoice.field.customer_address")),
                        ]),


                    Section::make('Date Details')
                        ->label(__('invoice.date_details'))
                        ->columns(4)
                        ->disabled(fn(Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                        ->schema([
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
                ]),
            Section::make('invoice items')
                ->label(__('invoice.invoice_items'))
                ->disabled(fn(Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                ->schema([
                    Forms\Components\Repeater::make('invoice_item')
                        ->relationship()
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
                                                    '3' => __("invoice.select.type3"),
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
                                                ->hidden(fn(Get $get): bool => $get('type') == '3')
                                                ->disabled(fn(Get $get): bool => !filled($get('type')))
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
                                                ->default(0)
                                                ->numeric()
                                                ->hidden(fn($operation, Get $get): bool => $operation == 'edit' || $get('type') == '3')
                                                ->label(fn(Get $get): string => $get('type') == 1 ? __("invoice.field.per_hour") : __("invoice.field.unit_price"))
                                                ->minValue(1)
                                                ->live()
                                                ->readOnly(fn(Get $get): bool => $get('type') == 1)
                                                ->disabled(fn(Get $get): bool => !filled($get('type')))
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
                                                ->required(fn($operation): bool => $operation == 'create'),
                                            Forms\Components\TextInput::make('price')
                                                ->label(__("invoice.field.price"))
                                                ->numeric()
                                                ->disabled(fn(Get $get): bool => !filled($get('type')))
                                                ->minValue(1)
                                                ->columnStart(4)
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
                    ->disabled(fn(Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                    ->label(__('invoice.info'))
                    ->description(__('invoice.here_you_can_define_invoice_footer'))
                    ->schema([
                        Forms\Components\Textarea::make('info')
                            ->label(__("invoice.field.information"))->rows(5)
                    ]),
                Section::make('invoice options')
                    ->disabled(fn(Livewire $livewire): bool => is_null($livewire->data['customer_id']))
                    ->label(__('invoice.invoice_options'))
                    ->description(__('invoice.here_you_can_define_invoice_options'))
                    ->schema([
                        Toggle::make('has_vat')
                            ->default(1)
                            ->label(__('invoice.has_vat'))
                    ])->grow(false),
            ])->from('md'),


        ])->columns(1);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rgnr')
                    ->label(__("invoice.field.rgnr"))->getStateUsing(fn($record) => $record->rgnr)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__("invoice.field.customer"))->getStateUsing(fn($record) => $record->customer->name)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_value')
                    ->label(__("invoice.field.invoice_value"))->getStateUsing(fn($record) => number_format($record->getTotalNetto(), 2, ',', '.')),
                Tables\Columns\TextColumn::make('date_origin')
                    ->label(__("invoice.field.date_origin"))->date('d.m.Y'),
                Tables\Columns\TextColumn::make('date_pay')
                    ->label(__("invoice.field.date_pay"))->date('d.m.Y'),
                Tables\Columns\ToggleColumn::make('payed')
                    ->label(__("invoice.field.payed")),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label(__("invoice.field.created_at"))->date('d.m.Y'),

                // Tables\Columns\TextColumn::make('#')
                //     ->getStateUsing(function ($record) {
                //         return '<a target="_blank" href="' . route('invoice.view', ['invoice' => $record->rgnr]) . '">' . __('invoice.link.view') . '</a>';
                //     })->html(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('not_send')
                    ->label(__('invoice.filter.not_sended'))
                    ->query(fn(Builder $query): Builder => $query->where('send', 0)),
                Tables\Filters\Filter::make('payed')
                    ->label(__('invoice.filter.payed'))
                    ->query(fn(Builder $query): Builder => $query->where('payed', 1)),
                Tables\Filters\Filter::make('has_vat')
                    ->label(__('invoice.filter.has_vat'))
                    ->query(fn(Builder $query): Builder => $query->where('has_vat', 1)),
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(__('invoice.field.customer'))
                    ->options(TenancyHelpers::getPluckCustomers())
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('reminder_email')
                        ->label(__('invoice.action.reminder_email'))
                        ->action(function(Livewire $livewire, Invoice $record) {
                            $livewire->sendReminderEmail($record);
                        })
                        ->icon('heroicon-m-chat-bubble-bottom-center-text'),
                        // ->url(fn(Invoice $record) => route('invoice.reminder', $record->rgnr)),
                    Tables\Actions\Action::make('duplicate')
                        ->label(__('invoice.action.duplicate'))
                        ->icon('heroicon-m-document-duplicate')
                        ->url(fn(Invoice $record) => route('invoice.duplicate', $record->rgnr)),
                ]),
                ActionGroup::make([
                    Tables\Actions\Action::make('viewInvoice')
                        ->label(__('invoice.action.view_invoice'))
                        ->icon('heroicon-m-eye')
                        ->url(fn(Invoice $record) => route('invoice.view', $record->rgnr), true),
                    Tables\Actions\Action::make('streamInvoice')
                        ->label(__('invoice.action.steam'))
                        ->icon('heroicon-m-printer')
                        ->url(fn(Invoice $record) => route('invoice.stream', $record->rgnr), true),
                    Action::make('generateXml')
                        ->label(__('invoice.action.generate_xml'))
                        ->icon('heroicon-m-code-bracket')
                        ->form([
                            Select::make('invoiceProfile')
                                ->label(__('invoice.action.invoiceProfile'))
                                ->options(config('zugferd-profiles.profiles'))
                                ->default(10)
                                ->required(),
                        ])
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->extraModalFooterActions([
                            Action::make('mergeWithPdf')
                                ->url(fn(Livewire $livewire, Invoice $record) => route('invoice.merge', ['rgnr' => $record->rgnr, 'profile' => $livewire->mountedTableActionsData[0]['invoiceProfile']]))
                                ->label(__('invoice.action.merge_with_pdf')),
                            Action::make('downloadXml')
                                ->url(fn(Livewire $livewire, Invoice $record) => route('invoice.ddxml', ['rgnr' => $record->rgnr, 'profile' => $livewire->mountedTableActionsData[0]['invoiceProfile']]))
                                ->label(__('invoice.action.xml_downlaod')),
                        ])
                ])

                    ->icon('heroicon-m-document-arrow-down')
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


    public static function getApiTransformer()
    {
        return InvoiceTransformer::class;
    }
}

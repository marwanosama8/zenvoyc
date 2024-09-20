<?php

namespace App\Filament\Company\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Offer;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Company\Resources\OfferResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Company\Resources\OfferResource\RelationManagers\CommentsRelationManager;
use App\Helpers\TenancyHelpers;
use App\Models\Customer;
use App\Tables\Columns\CopyUrl;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Livewire\Component as Livewire;
use Filament\Tables\Filters\Filter;
use function Laravel\Prompts\form;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    public static function getModelLabel(): string
    {
        return __('navigation.offer');
    }


    public static function getPluralModelLabel(): string
    {
        return __('navigation.offers');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.offer');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.project');
    }
    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('info')
                    ->label(__('offer.label.information'))
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->options(TenancyHelpers::getPluckCustomers())
                            ->searchable()

                            ->createOptionForm([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('customer.field.name'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('street')
                                            ->label(__('customer.field.street'))
                                            ->default('NA')
                                            ->required(),
                                        Forms\Components\TextInput::make('nr')
                                            ->label(__('customer.field.nr'))
                                            ->default('NA')
                                            ->required(),
                                        Forms\Components\TextInput::make('zip')
                                            ->label(__('customer.field.zip'))
                                            ->default('NA')
                                            ->required(),
                                        Forms\Components\TextInput::make('city')
                                            ->label(__('customer.field.city'))
                                            ->default('NA')
                                            ->required(),
                                        Forms\Components\TextInput::make('country')
                                            ->label(__('customer.field.country'))
                                            ->default('NA')
                                            ->required(),
                                        Forms\Components\TextInput::make('contact')
                                            ->label(__('customer.field.contact')),
                                        Forms\Components\TextInput::make('email')
                                            ->label(__('customer.field.email'))
                                            ->email(),
                                        Forms\Components\TextInput::make('cc')
                                            ->label(__('customer.field.cc')),
                                        Forms\Components\TextInput::make('vatid')
                                            ->label(__('customer.field.vatid')),
                                        Forms\Components\Textarea::make('options')
                                            ->columnSpanFull()
                                            ->label(__('customer.field.options')),
                                    ]),
                            ])
                            ->createOptionModalHeading(__('customer.grid.head'))
                            ->createOptionUsing(function (array $data): int {
                                return Customer::create($data)->id;
                            })
                            ->label(__("offer.field.customer"))
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('offer.field.title')
                            ->maxLength(255),
                    ]),
                Section::make([
                    Toggle::make('external_offer')
                        ->label(__('offer.external_offer'))
                        ->live(),
                ])
                    ->description(__('offer.section.description.Choose how to handle contract details: Toggle to add third-party offers via custom URLs or let our system automatically generate the contract by filling in key details.'))
                    ->label(__('offer.section.head.details')),
                Section::make('details')
                    ->schema([
                        Forms\Components\TextInput::make('external_offer_url')
                            ->required()
                            ->label(__("offer.field.external_offer_url"))
                            ->suffixIcon('heroicon-o-link')
                            ->hidden(fn(Livewire $livewire) => !$livewire->data['external_offer'])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('offer_value')
                            ->required()
                            ->numeric()
                            ->label(__("offer.field.offer_value"))
                            ->hidden(fn(Livewire $livewire) => !$livewire->data['external_offer'])
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('introtext')
                            ->required()
                            ->hidden(fn(Livewire $livewire) => $livewire->data['external_offer'])
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('positions')
                            ->label(__("offer.field.positions"))
                            ->orderColumn('order_column')
                            ->cloneable()
                            ->hidden(fn(Livewire $livewire) => $livewire->data['external_offer'])
                            ->addActionLabel(__("offer.field.add_item"))
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label(__("offer.field.description"))->rows(5)->required(),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label(__("offer.field.type"))
                                            ->required()
                                            ->options([
                                                '1' => __("offer.select.type1"),
                                                '2' => __("offer.select.type2"),
                                            ]),
                                        Forms\Components\TextInput::make('amount')
                                            ->label(__("offer.field.amount"))->numeric()->required(),
                                        Forms\Components\TextInput::make('price')
                                            ->label(__("offer.field.price"))->numeric()->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make([
                    Toggle::make('accepted')
                        ->label(__('offer.accepted'))
                        ->live(),
                    Toggle::make('general_access')
                        ->label(__('offer.general_access'))
                        ->live(),
                ])
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('offer.field.customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('offer.field.title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('offer_value')
                    ->label('offer.field.offer_value')
                    ->state(function (Offer $record) {
                        return Number::forHumans($record->getOfferValue(), maxPrecision: 2, abbreviate: true);
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('signature_date')
                    ->label('offer.field.signature_date')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('accepted')
                    ->label('offer.field.accepted')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('offer.field.created_at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('general_access')
                    ->alignCenter()
                    ->badge()
                    ->state(function (Offer $record) {
                        if ($record->external_offer) {
                            return ucfirst(__('offer.external_offer'));
                        } else {
                            return $record->general_access ? __('public') : __('private');
                        }
                    })
                    ->label(__('offer.table.general_access')),
                CopyUrl::make('external_offer_url')
                ->label('offer.label.offer_url')
                    ->customUrl(function (Offer $state): string {
                        return  $state->external_offer ?  $state->external_offer_url : url('sign-contract') . '/' . $state->token;
                    }),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Filter::make('accepted')
                    ->label(__('offer.filter.accepted'))
                    ->query(fn(Builder $query): Builder => $query->where('accepted', true))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
            CommentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
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

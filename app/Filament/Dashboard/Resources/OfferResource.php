<?php

namespace App\Filament\Dashboard\Resources;

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
use App\Filament\Dashboard\Resources\OfferResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use App\Filament\Dashboard\Resources\OfferResource\RelationManagers\CommentsRelationManager;
use App\Helpers\TenancyHelpers;
use App\Models\Customer;

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
    public static function form(Form $form): Form
    {
        return $form
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
                    ->maxLength(255),
                Forms\Components\RichEditor::make('introtext')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('positions')
                    ->label(__("offer.field.positions"))
                    ->orderColumn('order_column')
                    ->cloneable()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('signature_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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
                Tables\Columns\ToggleColumn::make('general_access')
                    ->alignCenter()
                    ->label(__('offer.table.general_access')),
                Tables\Columns\TextColumn::make('signed')
                    ->alignCenter()
                    ->label(__('offer.table.general_access'))
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('View Contract')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->action(fn (Offer $record) => redirect("sign-contract/{$record->token}")),
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

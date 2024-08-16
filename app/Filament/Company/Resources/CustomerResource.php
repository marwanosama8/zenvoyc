<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CustomerResource\Pages;
use App\Filament\Dashboard\Resources\CustomerResource\RelationManagers\CustomerContactsRelationManager;
use App\Helpers\Helpers;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getModelLabel(): string
    {
        return __('navigation.customer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.customers');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.customer');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.mangment');
    }
    protected static bool $isScopedToTenant = false;


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Fieldset::make('Inforamtions')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('customer.label.name'))
                            ->maxLength(200),
                        Forms\Components\TextInput::make('rate')
                            ->required()
                            ->label(__('customer.label.rate'))
                            ->numeric()
                            ->default(100.00),
                        Forms\Components\Textarea::make('added')
                            ->required()
                            ->label(__('customer.label.added'))
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label(__('customer.label.email'))
                            ->maxLength(200),
                        Forms\Components\TextInput::make('cc')
                            ->label(__('customer.label.cc'))
                            ->maxLength(200),
                        Forms\Components\TextInput::make('vat_id')
                            ->required()
                            ->label(__('customer.label.vat_id'))
                            ->maxLength(50),
                        Forms\Components\Textarea::make('options')
                            ->required()
                            ->label(__('customer.label.options'))
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->required()
                            ->label(__('customer.label.street'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('nr')
                            ->required()
                            ->label(__('customer.label.nr'))
                            ->maxLength(20),
                        Forms\Components\TextInput::make('zip')
                            ->required()
                            ->label(__('customer.label.zip'))
                            ->maxLength(20),
                        Select::make('country_id')
                            ->label(__('label.county_id'))
                            ->options(Helpers::getPluckCountries())
                            ->searchable()
                            ->required(),
                        TextInput::make('city')->label(__('city'))->required(),
                        Forms\Components\TextInput::make('contact')
                            ->label(__('customer.label.contact'))
                            ->maxLength(100),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('customer.label.name'))
                    ->searchable(),
                    Tables\Columns\TextColumn::make('reference')
                    ->label(__('customer.label.reference'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('customer.label.street'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('nr')
                    ->label(__('customer.label.nr'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip')
                    ->label(__('customer.label.zip'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label(__('customer.label.city'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('customer.label.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('cc')
                    ->label(__('customer.label.cc'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('vat_id')
                    ->label(__('customer.label.vat_id'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->label(__('customer.label.rate'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('general_access')
                    ->label(__('customer.label.general_access'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('reverse_charge')
                    ->label(__('customer.label.reverse_charge'))
                    ->sortable(),
                // Tables\Columns\TextColumn::make('id')
                //     ->formatStateUsing(fn (string $state): HtmlString => Helpers::customeHtmlElement('a' ,"href=''")),
                Tables\Columns\ViewColumn::make('token')
                    ->label(__('customer.label.invoices'))
                    ->view('filament.tables.columns.token-copy-column')
                    ->label('customer.field.token'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('customer.label.created_at'))

                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('customer.label.updated_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('customer.label.deleted_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            CustomerContactsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
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

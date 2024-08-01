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
                            ->maxLength(200),
                        Forms\Components\TextInput::make('rate')
                            ->required()
                            ->numeric()
                            ->default(100.00),
                        Forms\Components\Textarea::make('added')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(200),
                        Forms\Components\TextInput::make('cc')
                            ->maxLength(200),
                        Forms\Components\TextInput::make('vatid')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('options')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('nr')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('zip')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact')
                            ->maxLength(100),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cc')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('vatid')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('general_access')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('reverse_charge')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('id')
                //     ->formatStateUsing(fn (string $state): HtmlString => Helpers::customeHtmlElement('a' ,"href=''")),
                Tables\Columns\ViewColumn::make('token')
                    ->view('filament.tables.columns.token-copy-column')
                    ->label('customer.field.token'),
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

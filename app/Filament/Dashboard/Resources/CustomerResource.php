<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\CustomerResource\Pages;
use App\Models\Customer;
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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Inforamtions')
                    ->schema([
                        // ...
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Textarea::make('added')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('nr')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('zip')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('country')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('contact')
                            ->maxLength(100),
                    ]),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(200),
                Forms\Components\TextInput::make('cc')
                    ->maxLength(200),
                Forms\Components\TextInput::make('vatid')
                    ->maxLength(50),
                Forms\Components\TextInput::make('rate')
                    ->required()
                    ->numeric()
                    ->default(100.00),
                Forms\Components\Textarea::make('options')
                    ->columnSpanFull(),
                Fieldset::make('Contract')
                    ->schema([
                        Forms\Components\Repeater::make('customer_contacts')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('role')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('phone')
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('information')
                                    ->maxLength(65535)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->grid(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nr')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vatid')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('token')
                    ->label('customer.field.token')
                    ->copyable()
                    ->getStateUsing(fn (Customer $record): string =>  route('list.invoices', $record->token)),
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
                Tables\Columns\TextColumn::make('#')
                    ->getStateUsing(function ($record) {
                        return '<a target="_blank" href="' . route('list.invoices', ['token' => $record->token]) . '">' . __('invoice.link.invoice') . '</a>';
                    })
                    ->html()
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
            //
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

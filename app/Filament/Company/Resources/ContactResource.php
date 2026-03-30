<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\ContactResource\Pages;
use App\Filament\Company\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Grouping\Group;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('navigation.contact');
    }


    public static function getPluralModelLabel(): string
    {
        return __('navigation.contacts');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.contact');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.mangment');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('contract.name'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->label(__('contract.email'))

                    ->suffixIcon('heroicon-m-at-symbol')
                    ->maxLength(255),
                Forms\Components\TextInput::make('function')
                    ->label(__('contract.function'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label(__('contract.phone'))
                    ->suffixIcon('heroicon-m-phone')
                    ->maxLength(255),
                Forms\Components\TextInput::make('company')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label(__('contract.name'))
                            ->weight(FontWeight::Bold)
                            ->searchable(),
                        Tables\Columns\TextColumn::make('function')
                            ->label(__('contract.function'))
                            ->searchable(),
                    ]),
                    Stack::make([
                        Tables\Columns\TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->label(__('contract.email'))
                            ->searchable(),
                        Tables\Columns\TextColumn::make('phone')
                            ->icon('heroicon-m-phone')
                            ->label(__('contract.phone'))
                            ->searchable(),
                    ]),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->defaultGroup('company')
            ->groups([
                Group::make('company')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}

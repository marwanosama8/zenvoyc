<?php

namespace App\Filament\User\Resources\CustomerResource\RelationManagers;

use App\Helpers\TenancyHelpers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Actions\CreateAction;

class CustomerContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('contact.name')
                            ->weight(FontWeight::Bold)
                            ->searchable(),
                        Tables\Columns\TextColumn::make('function')
                            ->label('contact.function')
                            ->searchable(),
                    ]),
                    Stack::make([
                        Tables\Columns\TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->label('contact.email')
                            ->copyable()
                            ->searchable(),
                        Tables\Columns\TextColumn::make('phone')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->label('contact.phone')
                            ->searchable(),
                    ]),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
                CreateAction::make()
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('contract.name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('contract.email')

                            ->suffixIcon('heroicon-m-at-symbol')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('function')
                            ->label('contract.function')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('contract.phone')
                            ->suffixIcon('heroicon-m-phone')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}

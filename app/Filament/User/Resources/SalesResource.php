<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SalesResource\Pages;
use App\Filament\User\Resources\SalesResource\RelationManagers;
use App\Models\Sales;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Porject';
  
    public static function getModelLabel(): string
    {
        return __('navigation.sale');
    }

  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.sale_items');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.sale_item');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.finance');
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Name')->required()->columns(2),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->columns(2),
                Textarea::make('description')->label('Descriptin')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable(),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Company\Resources;

use App\Enums\FrequencyEnums;
use App\Filament\Company\Resources\ExpenditureResource\Pages;
use App\Filament\Company\Resources\ExpenditureResource\RelationManagers;
use App\Models\Expenditure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenditureResource extends Resource
{
    protected static ?string $model = Expenditure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('expenditure.field.name')
                    ->required(),
                Textarea::make('description')
                    ->label('expenditure.field.description')
                    ->required(),
                TextInput::make('cost')
                    ->label('expenditure.field.cost')
                    ->required()
                    ->numeric(),
                Select::make('frequency')
                    ->label('expenditure.field.frequency')
                    ->options(FrequencyEnums::class)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('expenditure.field.name'),
                Tables\Columns\TextColumn::make('description')
                    ->label('expenditure.field.description'),
                Tables\Columns\TextColumn::make('cost')
                    ->label('expenditure.field.cost'),
                    Tables\Columns\SelectColumn::make('frequency')
                    ->label('expenditure.field.frequency')
                    ->options(FrequencyEnums::class)

            ])
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
            'index' => Pages\ListExpenditures::route('/'),
            'create' => Pages\CreateExpenditure::route('/create'),
            'edit' => Pages\EditExpenditure::route('/{record}/edit'),
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

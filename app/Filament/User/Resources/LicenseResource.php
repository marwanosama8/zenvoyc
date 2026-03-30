<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\LicenseResource\Pages;
use App\Filament\User\Resources\LicenseResource\RelationManagers;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
  
    public static function getModelLabel(): string
    {
        return __('navigation.license');
    }

  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.licenses');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.license');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.project');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label(__('license.field.name'))
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('total_volume')
                    ->required()
                    ->label(__('license.field.total_volume'))
                    ->numeric(),
                Forms\Components\TextInput::make('remaining_volume')
                    ->required()
                    ->label(__('license.field.remaining_volume'))
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->label(__('license.field.price'))
                    ->prefix('$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('license.field.name'))

                    ->searchable(),
                Tables\Columns\TextColumn::make('total_volume')
                    ->numeric()
                    ->label(__('license.field.total_volume'))

                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_volume')
                    ->numeric()
                    ->label(__('license.field.remaining_volume'))

                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->label(__('license.field.price'))

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
                    ->sortable(),
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
            RelationManagers\CustomersRelationManager::class,
            RelationManagers\NoticeLicenseRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),

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

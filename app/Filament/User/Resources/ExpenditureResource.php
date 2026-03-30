<?php

namespace App\Filament\User\Resources;

use App\Enums\FrequencyEnums;
use App\Filament\User\Resources\ExpenditureResource\Pages;
use App\Filament\User\Resources\ExpenditureResource\RelationManagers;
use App\Models\Expenditure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Return_;

class ExpenditureResource extends Resource
{
    protected static ?string $model = Expenditure::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';

    public static function getModelLabel(): string
    {
        return __('navigation.expenditure');
    }


    public static function getPluralModelLabel(): string
    {
        return __('navigation.expenditures');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.expenditure');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.finance');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('info')
                    ->label(__('expenditure.label.info'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('expenditure.field.name'))
                            ->required(),
                        TextInput::make('cost')
                            ->label(__('expenditure.field.cost'))
                            ->required()
                            ->numeric(),
                        Textarea::make('description')
                            ->label(__('expenditure.field.description'))
                            ->columnSpanFull()
                            ->required(),
                    ]),
                Fieldset::make('date')
                    ->label(__('expenditure.label.date'))
                    ->schema([
                        Select::make('frequency')
                            ->label(__('expenditure.field.frequency'))
                            ->default(FrequencyEnums::OneTime)
                            ->options(FrequencyEnums::class)
                            ->live()
                            ->required(),
                        Select::make('start')
                            ->label(__('expenditure.field.start'))
                            ->live()
                            ->default(Carbon::today()->toDateString())
                            ->options(fn(Get $get, $livewire): array => match ($get('frequency')->value ?? $get('frequency')) {
                                'one-time' => $livewire->getStartDateOption('one-time'),
                                'monthly' => $livewire->getStartDateOption('monthly'),
                                'yearly' =>  $livewire->getStartDateOption('yearly'),
                                default => [],
                            })
                            ->native(0)
                            ->required(),
                        Select::make('end')
                            ->label(__('expenditure.field.end'))
                            ->hidden(function (Get $get) {
                                if ($get('frequency') instanceof FrequencyEnums) {
                                    return $get('frequency')->value  == 'one-time';
                                } else {
                                    return $get('frequency') == 'one-time';
                                }
                            })
                            ->options(fn(Get $get, $livewire): array => match ($get('frequency')->value ?? $get('frequency')) {
                                'one-time' => $livewire->getEndDateOption('one-time', $get('start')),
                                'monthly' => $livewire->getEndDateOption('monthly',  $get('start')),
                                'yearly' =>  $livewire->getEndDateOption('yearly', $get('start')),
                                default => [],
                            })
                            ->native(0)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('expenditure.field.name')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('expenditure.field.description')),
                Tables\Columns\TextColumn::make('cost')
                    ->label(__('expenditure.field.cost')),
                Tables\Columns\TextColumn::make('frequency')
                    ->getStateUsing(function ($record) {
                        return ucfirst($record->frequency->value);
                    })
                    ->label(__('expenditure.field.frequency'))
                    ->badge()
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

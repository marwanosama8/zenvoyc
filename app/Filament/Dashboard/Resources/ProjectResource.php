<?php

namespace App\Filament\Dashboard\Resources;
use App\Filament\Company\Resources\ProjectResource\Pages;
use App\Filament\Company\Resources\ProjectResource\RelationManagers;
use App\Helpers\TenancyHelpers;
use App\Models\Customer;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static bool $isDiscovered = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('filament-timesheet::project.projects');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-timesheet::project.projects');
    }

    public static function getModelLabel(): string
    {
        return __('filament-timesheet::project.project');
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
                    ->label(__('filament-timesheet::project.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('hourly_rate')
                    ->label(__('filament-timesheet::project.hourly_rate'))
                    ->numeric()
                    ->required(),
                Select::make('customer_id')
                    ->required()
                    ->relationship('customer', 'name')
                    ->options(TenancyHelpers::getPluckCustomers())
                    ->label(__('filament-timesheet::project.client'))
                    ->searchable()
                    ->preload()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-timesheet::project.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('filament-timesheet::project.client')),
            ])
            ->groups([
                Group::make('customer.name')
                    ->label(__('filament-timesheet::project.client')),
            ])
            ->defaultGroup('customer.name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}

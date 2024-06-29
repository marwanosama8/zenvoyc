<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\EmployeeResource\Pages;
use App\Filament\Company\Resources\EmployeeResource\RelationManagers;
use App\Helpers\TenancyHelpers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
  
    public static function getModelLabel(): string
    {
        return __('navigation.employee');
    }

  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.employees');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.employee');
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
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('public_name')
                        ->required()
                        ->nullable()
                        ->helperText('This is the name that will be displayed publicly (for example in blog posts).')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->helperText(fn (string $context): string => ($context !== 'create') ? __('Leave blank to keep the current password.') : '')
                        ->maxLength(255),
                    Forms\Components\Select::make('roles')
                        ->relationship(
                            'roles',
                            'name',
                            modifyQueryUsing: fn (Builder $query) => $query->whereIn('name', ['employee']),
                        )
                        ->multiple()
                        ->preload()
                        ->required(),
                    Forms\Components\Checkbox::make('is_blocked')
                        ->label('Is Blocked?')
                        ->helperText('If checked, this user will not be able to log in or use any services provided.')
                        ->default(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $tenancyUsersIds = TenancyHelpers::getTenant()->users->pluck('id');
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(config('app.datetime_format')),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(config('app.datetime_format')),
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('id', $tenancyUsersIds))
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

}

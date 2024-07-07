<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\TimesheetResource\Pages;
use App\Filament\Employee\Resources\TimesheetResource\RelationManagers;
use App\Helpers\TenancyHelpers;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Context;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    public static function getNavigationGroup(): ?string
    {
        return __('navigation.project');
    }  
    public static function getModelLabel(): string
    {
        return __('navigation.timesheet');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.timesheet');
    }
  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.timesheets');
    }
    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Select::make('timesheet_tasks')
                    ->relationship(titleAttribute: 'name')
                    ->options(TenancyHelpers::getPluckTasksByProjectKey())
                    ->multiple(),
                Forms\Components\DatePicker::make('date')->default(now()),
                Forms\Components\TimePicker::make('start_time')->required()
                    ->live(),
                Forms\Components\TimePicker::make('end_time')
                    ->required(fn (Get $get): bool => !filled($get('manual_time')))
                    ->live()
                    ->disabled(fn (Get $get): bool => !empty($get('manual_time'))),
                Forms\Components\TextInput::make('manual_time')
                    ->required(fn (Get $get): bool => !filled($get('end_time')))
                    ->disabled(fn (Get $get): bool => !empty($get('end_time') && empty($get('manual_time'))))
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('end_time', \App\Helpers\CalculationHelpers::getEndTimeAfterParseToTimeString($get('start_time'), $get('manual_time'))))
                    ->hint('you can add the hours here manually, ex: 3h 30m'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->modifyQueryUsing(fn (Builder $query) => $query->authEmployeeTimesheet())
            ->columns([
                Tables\Columns\TextColumn::make('Task')
                    ->label(__('timesheet.task'))
                    ->getStateUsing(fn (Timesheet $record) => $record->timesheet_tasks->pluck('title')->implode('-')),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('timesheet.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_time')
                    ->label(__('timesheet.estimated_time'))
                    ->state(fn (Timesheet $record): string => Carbon::parse($record->start_time)->format('H:m') . ' -> ' .   Carbon::parse($record->end_time)->format('H:m'))
                    ->description(function (Timesheet $record): string {
                        $startTime = Carbon::parse($record->start_time);
                        $endTime = Carbon::parse($record->end_time);
                        return $startTime->diff($endTime)->format('%H:%I');
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('timesheet.is_active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('timesheet.created_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('timesheet.updated_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label(__('filament-timesheet::timesheet.created_from')),
                        Forms\Components\DatePicker::make('created_until')->label(__('filament-timesheet::timesheet.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                Filter::make('project_id')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->options(TenancyHelpers::getPluckProjects())
                            ->label(__('filament-timesheet::timesheet.project')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['project_id'] ?? null,
                                function (Builder $query, $projectId) {
                                    $query->whereHas('timesheet_tasks', function (Builder $query) use ($projectId) {
                                        $query->where('project_id', $projectId);
                                    });
                                }
                            );
                    })
                    ->label(__('filament-timesheet::timesheet.project')),
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
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\TimesheetResource\Pages;
use App\Helpers\TenancyHelpers;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static bool $isDiscovered = false;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getModelLabel(): string
    {
        return __('navigation.timesheet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.timesheets');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.timesheet');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.project');
    }
    protected static bool $isScopedToTenant = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tasks')
                    ->relationship('timesheet_tasks', 'name')
                    ->required()
                    ->multiple()
                    ->options(TenancyHelpers::getPluckProjects())
                    ->label(__('filament-timesheet::timesheet.project'))
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('start_time'),
                Forms\Components\DateTimePicker::make('end_time'),
                Forms\Components\TextInput::make('manual_time')
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                ->label(__('timesheet.date')),
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
                            ->label(__('filament-timesheet::timesheet.filter.project_id')),
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
                Filter::make('employee')
                    ->form([
                        Forms\Components\Select::make('employee')
                            ->multiple()
                            ->options(TenancyHelpers::getPluckCompanyEmployeeNames())
                            ->label(__('filament-timesheet::timesheet.filter.employees')),
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query->when(
                                count($data['employee']),
                                function (Builder $query) use ($data) {
                                    $query->whereIn('employee_id', $data['employee']);
                                }
                            );
                        }
                    )
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

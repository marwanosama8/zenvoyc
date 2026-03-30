<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TaskResource\Pages;
use App\Filament\User\Resources\TaskResource\RelationManagers;
use App\Helpers\TenancyHelpers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split as LayoutSplit;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Str;

class TaskResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.project');
    }
    public static function getModelLabel(): string
    {
        return __('navigation.task');
    }


    public static function getPluralModelLabel(): string
    {
        return __('navigation.tasks');
    }
    public static function form(Form $form): Form
    {
        $priority = array_map(fn ($e) => __($e), config('tasks.priority'));
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Checkbox::make('done')
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        if ($state) {
                                            $done = array_map(function ($task) {
                                                $task['done_subtask'] = true;
                                                return $task;
                                            }, $get('subtasks'));
                                            $set('subtasks', $done);
                                        }
                                    })

                                    ->label('Done')
                                    ->extraAttributes(['class' => 'm-4'])
                                    ->inline(false),
                            ])
                            ->columns(10),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->disabled(function (Get $get) {
                                        return $get('done');
                                    })
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->disabled(function (Get $get) {
                                        return $get('done');
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Forms\Components\Section::make('Subtasks')
                            ->headerActions([])
                            ->schema([
                                Repeater::make('subtasks')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->schema([
                                        Forms\Components\Checkbox::make('done_subtask')
                                            ->label('Done')
                                            ->inline(0)
                                            ->columnSpan([
                                                'md' => 1,
                                            ])->live(),
                                        Forms\Components\TextInput::make('title')
                                            ->readOnly()
                                            ->columnSpan([
                                                'md' => 9,
                                            ]),
                                    ])->grow(1)->columns([
                                        'md' => 10,
                                    ])
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TagsInput::make('tags'),
                        Forms\Components\Select::make('priority')
                            ->options($priority),
                        Forms\Components\Select::make('project_id')
                            ->native(0)
                            ->label('project.project_id')
                            ->required()
                            ->options(TenancyHelpers::getPluckProjects())
                    ])
                    ->columnSpan(['lg' => 1])
                // ->hidden(fn (?Task $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LayoutSplit::make([
                    Tables\Columns\CheckboxColumn::make('done')
                        ->grow(false),
                    Tables\Columns\TextColumn::make('title')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('priority')
                        ->alignCenter()
                        ->getStateUsing(fn ($record): ?string =>  !is_null($record->priority) ? config('tasks.priority.' . $record->priority)  : config('tasks.priority.1'))

                        ->badge(),
                    Tables\Columns\TextColumn::make('tags')
                        ->icon('heroicon-m-tag')
                        ->badge(),
                    // Tables\Columns\TextColumn::make('subtasks')
                    //     ->alignCenter()
                    //     ->weight(FontWeight::Bold)

                    //     ->getStateUsing(function ($record): ?string {
                    //         $uncompletedTasks = array_filter($record->subtasks, function ($task) {
                    //             return $task['done_subtask'] === false;
                    //         });

                    //         return 'has ' .  count($uncompletedTasks) . ' Uncompleted Subtasks';
                    //     }),

                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ])
            ->filters([
                Filter::make('tags')
                    ->form([
                        TagsInput::make('tags'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tags'],
                                fn (Builder $query, $date): Builder => $query->whereJsonContains('tags', $date)
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultGroup('priority')
            ->groups([
                Group::make('priority')
                    ->getTitleFromRecordUsing(
                        function (Task $record) {
                            return __(config('tasks.priority.' . $record->priority ?? '1'));
                        }
                    ),
                Group::make('done')
                    ->getTitleFromRecordUsing(
                        function (Task $record) {
                            return $record->done ? 'Completed' : 'Uncompleted';
                        }
                    ),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}

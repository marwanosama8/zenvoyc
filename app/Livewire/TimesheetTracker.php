<?php

namespace App\Livewire;

use App\Helpers\TenancyHelpers;
use App\Models\Task;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimesheetTracker extends Component implements HasForms
{
    use InteractsWithForms;

    public $startTime;
    public $manualTimeInput;
    public $isTracking;
    public $elapsedTime = '00:00:00';
    public $timesheetId;
    public $validationStr;
    public ?array $defaultTasks = [];
    public ?array $data = [];

    public function mount()
    {
        $activeTimesheet = Timesheet::where('is_active', true)->first();

        $this->setValidationString();

        if ($activeTimesheet) {
            $this->setSyncTasks($activeTimesheet->timesheet_tasks->pluck('id')->toArray());
            $this->timesheetId = $activeTimesheet->id;
            $this->startTime = Carbon::parse($activeTimesheet->start_time);
            $this->isTracking = true;
            $this->updateElapsedTime();
        }

        $this->form->fill();
    }

    public function startTracking()
    {
        $this->startTime = Carbon::now();
        $this->isTracking = true;
        $this->updateElapsedTime();

        $timesheet = Timesheet::create([
            'date' => $this->startTime,
            'start_time' => $this->startTime,
            'is_active' => true,
        ]);

        $timesheet->timesheet_tasks()->sync($this->data['tasks']);

        $this->timesheetId = $timesheet->id;
        
    }

    public function stopTracking()
    {
        $this->isTracking = false;
        $endTime = Carbon::now();

        $timesheet = Timesheet::find($this->timesheetId);
        $timesheet->update([
            'end_time' => $endTime,
            'is_active' => false,
        ]);

        $this->elapsedTime = '00:00:00';
        $this->reset(['startTime', 'elapsedTime', 'timesheetId']);
    }


    public function updateElapsedTime()
    {
        if ($this->isTracking && $this->startTime) {
            
            $startTime = $this->startTime;
            $now = Carbon::now();
            $diff = $startTime->diffInSeconds($now,false);
            $hours = floor($diff / 3600);
            $minutes = floor(($diff / 60) % 60);
            $seconds = $diff % 60;
            $this->elapsedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tasks')
                    ->options(TenancyHelpers::getPluckTasksByProjectKey())
                    ->default($this->defaultTasks)
                    ->disabled($this->isTracking ?? false)
                    ->multiple(),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.timesheet-tracker');
    }

    public function setValidationString()
    {
        $this->validationStr = __('validation.choose_task_first');
    }

    public function setSyncTasks($tasksId)
    {
        $this->defaultTasks = $tasksId;
    }
}

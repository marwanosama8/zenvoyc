<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\TimesheetScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

#[ScopedBy([TimesheetScope::class])]
class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time',
        'date',
        'manual_time',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
    ];


    public function timesheetable()
    {
        return $this->morphTo();
    }

    /**
     * The timesheet_tasks that belong to the Timesheet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function timesheet_tasks()
    {
        return $this->belongsToMany(Task::class, 'task_timesheet', 'timesheet_id', 'task_id');
    }

    /**
     * Get the user that owns the Timesheet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAuthEmployeeTimesheet($query)
    {
        $user = auth()->user();

        if ($user) {
            return $query->where('employee_id', $user->id);
        }
    }

    public function setHoursAttribute($value): void
    {
        $value = str_replace(',', '.', $value);

        if (str_contains($value, ':')) {
            [$hours, $minutes] = explode(':', $value);
            $this->attributes['hours'] = (int) $hours + ($minutes / 60);
        } else {
            $this->attributes['hours'] = $value;
        }
    }

    public function scopeThisWeek(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeLastWeek(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->subWeek()->startOfWeek()->toDateString(), Carbon::now()->subWeek()->endOfWeek()->toDateString()]);
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
    }

    public function scopeThisQuarter(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->startOfQuarter()->toDateString(), Carbon::now()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastQuarter(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->subQuarter()->startOfQuarter()->toDateString(), Carbon::now()->subQuarter()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastMonth(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->subMonth()->startOfMonth()->toDateString(), Carbon::now()->subMonth()->endOfMonth()->toDateString()]);
    }

    public function scopeThisYear(Builder $query): void
    {
        $query->whereBetween('date', [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()]);
    }

    protected static function booted(): void
    {
        static::creating(function (Timesheet $model) {

            $currentTenant = TenancyHelpers::getTenant();
            if (empty($model->timesheetable_type)) {
                $model->timesheetable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->timesheetable_id)) {
                $model->timesheetable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
            if (!is_null(Filament::getTenant()) && TenancyHelpers::isEmployee()) {
                $model->employee_id =  auth()->id();
            }
        });
    }
}

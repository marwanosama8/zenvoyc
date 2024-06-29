<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\TaskScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

#[ScopedBy([TaskScope::class])]
class Task extends Model
{
    use HasFactory,HasFilamentComments;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'subtasks',
        'tags',
        'priority',
        'done',
    ];

    protected $casts = [
        'tags' => 'array',
        'subtasks' => 'array',
        'done' => 'boolean',
    ];

    /**
     * The employee_tasks that belong to the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employee_tasks()
    {
        return $this->belongsToMany(User::class, 'employee_tasks', 'task_id', 'user_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Task $model) {

            $currentTenant = TenancyHelpers::getTenant();
            $model->company_id = $currentTenant->id;
        });
    }

    public function getDoneAttribute($value)
    {
        return $value ? true : false;
    }

    public function scopeAuthEmployeeTasks($query)
    {
        $user = auth()->user();

        if ($user) {
            return $query->whereHas('employee_tasks', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
    }
}

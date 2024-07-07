<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hourly_rate',
        'manual_timesheet',
    ];

    /**
     * Get the user that owns the EmployeeSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

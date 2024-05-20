<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
    ];

    // Owner Realtionship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

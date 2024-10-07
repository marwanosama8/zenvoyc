<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'features',
        'is_popular',
        'is_default',
        'role_id',
        'metadata',
    ];

    protected $casts = [
        'features' => 'array',
        'metadata' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTheme extends Model
{
    protected $fillable = [
        'aliases',
        'name',
        'colors',
        'is_active',
    ];
    protected $casts = [
        'colors' => 'array',
        'is_active' => 'boolean',
    ];

}

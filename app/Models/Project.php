<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\ProjectScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([ProjectScope::class])]
class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id',
        'name',
        'hourly_rate',
    ];

    public function projectable()
    {
        return $this->morphTo();
    }

    
    /**
     * Get the customer that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Project $model) {

            $currentTenant = TenancyHelpers::getTenant();
       
            if (empty($model->projectable_type)) {
                $model->projectable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->projectable_id)) {
                $model->projectable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}

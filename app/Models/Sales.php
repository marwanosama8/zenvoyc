<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\SalesScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([SalesScope::class])]
class Sales extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    public function salesable()
    {
        return $this->morphTo();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Sales $model) {
            $currentTenant = TenancyHelpers::getTenant();

            if (empty($model->salesable_type)) {
                $model->salesable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->salesable_id)) {
                $model->salesable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}

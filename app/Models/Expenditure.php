<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\FrequencyEnums;
use App\Helpers\TenancyHelpers;
use App\Models\Scopes\ExpenditureScope;
use App\Observers\ExpenditureObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([ExpenditureScope::class])]
class Expenditure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'frequency',
        'end',
        'start'
    ];

    protected $casts = [
        'frequency' => FrequencyEnums::class,
        'start' => 'datetime:Y-m-d',
        'end' => 'datetime:Y-m-d'
    ];

    public function expenditureable()
    {
        return $this->morphTo();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Expenditure $model) {
            $currentTenant = TenancyHelpers::getTenant();
            $model->expenditureable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            $model->expenditureable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
        });
    }
}

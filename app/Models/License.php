<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\LicenseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([LicenseScope::class])]
class License extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'total_volume',
        'remaining_volume',
        'price',
        // 'company_id'
    ];

    // Owner Realtionship
    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }

    /**
     * The customers that belong to the License
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'license_allocations', 'license_id', 'customer_id')->withPivot(['volume'])->withTimestamps();
    }

    public function licenseable()
    {
        return $this->morphTo();
    }
    /**
     * Get all of the notice_license for the License
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notice_license()
    {
        return $this->hasMany(LicenseNotice::class);
    }

    protected static function booted(): void
    {
        static::creating(function (License $model) {

            $currentTenant = TenancyHelpers::getTenant();
       
            if (empty($model->licenseable_type)) {
                $model->licenseable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->licenseable_id)) {
                $model->licenseable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}
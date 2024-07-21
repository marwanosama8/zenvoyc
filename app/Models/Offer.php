<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\OfferScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([OfferScope::class])]
class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'general_access',
        'token',
        'title',
        'introtext',
        'positions',
        'signature',
        'signature_name',
        'signature_date',
        'signed',
    ];

    protected $casts = [
        'positions' => 'array',
    ];

    // Owner Realtionship
    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }
    
    public function offerable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * Get the customer that owns the Offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all of the comments for the Offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(OfferComment::class);
    }
    
	protected static function booted(): void
    {
        static::creating(function (Offer $model) {

            $currentTenant = TenancyHelpers::getTenant();
       
            if (empty($model->offerable_type)) {
                $model->offerable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->offerable_id)) {
                $model->offerable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}

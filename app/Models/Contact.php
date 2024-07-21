<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\ContactScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([ContactScope::class])]
class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'function',
        'company',
    ];


    /**
     * The customer_contact that belong to the Contact
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_contacts', 'contact_id', 'customer_id');
    }

    public function contactable()
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::creating(function (Contact $model) {

            $currentTenant = TenancyHelpers::getTenant();
            if (empty($model->contactable_type)) {
                $model->contactable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->contactable_id)) {
                $model->contactable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}

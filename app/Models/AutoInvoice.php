<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Models\Scopes\AutoInvoiceScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([AutoInvoiceScope::class])]
class AutoInvoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'customer_id',
        'user_id',
        'rgnr',
        'customer_address',
        'rate',
        'info',
        'ust',
        'options',
        'items',
        'custom_interval',
        'next_generate_date',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    // Owner Realtionship
    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function autoInvoiceable()
    {
        return $this->morphTo();
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted(): void
    {
        static::creating(function (AutoInvoice $model) {

            $currentTenant = TenancyHelpers::getTenant();
            if (empty($model->auto_invoiceable_type)) {
                $model->auto_invoiceable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->auto_invoiceable_id)) {
                $model->auto_invoiceable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }

}

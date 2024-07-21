<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;
use App\Models\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([CustomerScope::class])]
class Customer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        // 'company_id',
        // 'customer_id',
        'name',
        'street',
        'nr',
        'zip',
        'city',
        'country',
        'email',
        'token',
        'cc',
        'contact',
        'rate',
        'vatid',
        'options',
        'general_access',
        'reverse_charge',
    ];
    // Owner Realtionship
    public function customerable()
    {
        return $this->morphTo();
    }



    public function Invoice()
    {
        return $this->hasMany(TenantInvoice::class);
    }

    public function getAllInvoices()
    {
        return TenantInvoice::where('customer_id', $this->id)->get();
    }

    public static function getRate($customerID)
    {
        return self::find($customerID)->rate;
    }

    public function getCLV()
    {
        $CLV = 0;
        foreach ($this->getAllInvoices() as $invoice) {
            $CLV += $invoice->getTotalNetto();
        }
        return number_format($CLV, 2, ',', '.');
    }

    public function getLastYear()
    {
        $data = TenantInvoice::where('customer_id', $this->id)->whereYear('date_origin', '=', Carbon::now()->subYear()->format('Y'))->get();
        $result = 0;
        foreach ($data as $invoice) {
            $result += $invoice->getTotalNetto();
        }
        return number_format($result, 2, ',', '.');
    }

    public function getAddress()
    {
        $html = $this->name . '<br>' . $this->added . '<br>' . $this->street . ' ' . $this->nr . '<br>' . $this->zip . ' ' . $this->city . '<br>';
        if ($this->country !== 'Germany') {
            $html .= $this->country . '<br>';
        }
        if ($this->vatid !== null) {
            $html .= $this->vatid . '<br>';
        }
        return $html;
    }

    public function getalldata()
    {
        return self::with('Invoice');
    }

    // public function contacts()
    // {
    //     return $this->hasMany(Contact::class);
    // }

    /**
     * The licenses that belong to the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function licenses()
    {
        return $this->belongsToMany(License::class, 'license_allocations', 'customer_id', 'license_id')->withTimestamps();
    }


    /**
     * The customer_contacts that belong to the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'customer_contacts', 'customer_id', 'contact_id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Customer $model) {

            if (empty($model->token)) {
                $model->token = Str::random(20);
            }
            $currentTenant = TenancyHelpers::getTenant();

            if (empty($model->customerable_type)) {
                $model->customerable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            }
            if (empty($model->customerable_id)) {
                $model->customerable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
            }
        });
    }
}

<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\InvoiceScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Str;

#[ScopedBy([InvoiceScope::class])]
class Invoice extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'customer_id',
		// 'user_id',
		'rgnr',
		'customer_address',
		'date_origin',
		'date_start',
		'date_end',
		'date_pay',
		'rate',
		'info',
		'ust',
		'printed',
		'send',
		'payed',
		'monthely',
		'regenerated',
		'options',
	];
	// protected $with = ['Customer'];
	// protected $rules = [
	// 	'customer_id' => 'required',
	// ];
	protected $guarded = [];

	// Owner Realtionship
	// public function company()
	// {
	// 	return $this->belongsTo(Company::class);
	// }



	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function InvoiceItem()
	{
		return $this->hasMany(InvoiceItem::class);
	}

    //just for Filament 3
    public function invoice_item()
    {
        return $this->hasMany(InvoiceItem::class);
    }


    public function invoiceable()
    {
        return $this->morphTo();
    }

	public function addInvoiceItem($description, $amount, $type)
	{
		$amount = str_replace(',', '.', $amount);

		$newItem = new InvoiceItem();
		$newItem->invoice_id = $this->id;
		$newItem->description = $description;
		$newItem->amount = $amount;
		$newItem->type = $type;
		$newItem->price = self::getPriceAmount($type, $amount, $this->rate);
		$newItem->save();
	}

	public static function getPriceAmount($type, $amount, $rate)
	{
		$amount = str_replace(',', '.', $amount);

		switch ($type) {
			case '1':
				return $amount;

			case '2':
				return $amount * $rate;
		}
		return null;
	}

	public static function getNextNr($value = null)
	{
		if ($value !== null) {
			$model = self::where('rgnr', $value)->first();
			if ($model !== null) {
				return $value;
			}
		}
		$rgnr = Setting::get('rgnr');
		Setting::set('rgnr', $rgnr + 1);

		return date('Y') . '-' . $rgnr;
	}

	public function getDateStartAttribute($value)
	{
		return Carbon::parse($value)->format('d.m.Y');
	}

	public function getDateEndAttribute($value)
	{
		return Carbon::parse($value)->format('d.m.Y');
	}

	public function getDatePayAttribute($value)
	{
		return Carbon::parse($value)->format('d.m.Y');
	}

	public function getDateOriginAttribute($value)
	{
		return Carbon::parse($value)->format('d.m.Y');
	}

	public function getTotalNetto(){
		return $this->invoice_item()->sum('price');
	}

	public function getTotalNettoQuarter()
	{
		$time = Carbon::now();
		return $this->Items()->where('created_at', '>=', $time->startOfQuarter())->sum('price');
	}

	public function getCurrentVat()
	{
		/**
		 * Corona UsT Check for 19% => 16% UST between 01.07.2020 - 31.12.2020
		 */
		$CoronaUSTStart = Carbon::createFromFormat('Y-m-d', '2020-07-01');
		$CoronaUSTEnd = Carbon::createFromFormat('Y-m-d', '2020-12-31');
		$CoronaCheck = Carbon::createFromFormat('d.m.Y', $this->date_origin)->between($CoronaUSTStart, $CoronaUSTEnd);

		if ($CoronaCheck) {
			return 16;
		}
		return 19;
	}

	public function getTotalVat()
	{
		return (($this->getTotalNetto() / 100) * $this->getCurrentVat());
	}

	public function getPercentVat()
	{
		return $this->getCurrentVat();
	}

	public function getQuarterVat()
	{
		return (($this->getTotalNettoQuarter() / 100) * 19);
	}

	public function getTotalBrutto()
	{
		return ($this->getTotalNetto() + $this->getTotalVat());
	}

	/**
	 * Get all of the invoice_media for the Invoice
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function invoiceMedia()
	{
		return $this->hasMany(InvoiceMedia::class);
	}

	/**
	 * Get the customer associated with the Invoice
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	protected static function booted(): void
    {
        static::creating(function (Invoice $model) {

            $currentTenant = TenancyHelpers::getTenant();
            $model->invoiceable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
            $model->invoiceable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
        });
    }
}

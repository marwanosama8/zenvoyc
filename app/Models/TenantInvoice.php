<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\TenantInvoiceScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Str;

#[ScopedBy([TenantInvoiceScope::class])]
class TenantInvoice extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'customer_id',
		'rgnr',
		'customer_address',
		'date_origin',
		'date_start',
		'date_end',
		'date_pay',
		'rate',
		'info',
		'has_vat',
		'vat_percent',
		'send',
		'payed',
		'monthely',
	];

	protected $guarded = [];




	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function InvoiceItem()
	{
		return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
	}

	//just for Filament 3
	public function invoice_item()
	{
		return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
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
	public function getTotalAttribute($value)
	{
		return $this->invoice_item()->sum('price');
	}

	public function getCustomerNameAttribute($value)
	{
		return $this->customer->name;
	}
	public function getCustomerEmailAttribute($value)
	{
		return $this->customer->email;
	}
	public function getNumberAttribute($value)
	{
		return $this->rgnr;
	}

	public function getTotalNetto()
	{
		return $this->invoice_item()->sum('price');
	}

	public function getTotalNettoQuarter()
	{
		$time = Carbon::now();
		return $this->Items()->where('created_at', '>=', $time->startOfQuarter())->sum('price');
	}

	public function getCurrentVat()
	{
		return $this->invoiceable->settings->vat_percent;
	}

	public function getTotalVat()
	{
		return ($this->getTotalNetto() / 100) * $this->getCurrentVat();
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

	public function scopeHasBeenPaid($query)
	{
		return $query->where('payed', 1);
	}

	public function scopeHasNotPayed($query)
	{
		return $query->where('payed', 0);
	}	

	/**
	 * Get all of the invoice_media for the Invoice
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function invoiceMedia()
	{
		return $this->hasMany(InvoiceMedia::class, 'invoice_id', 'id');
	}

	/**
	 * Get the customer associated with the Invoice
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class)->withoutGlobalScopes();
	}

	protected static function booted(): void
	{
		static::creating(function (TenantInvoice $model) {

			$currentTenant = TenancyHelpers::getTenant();
			if (empty($model->rgnr)) {
				$model->rgnr = self::getNextNr();
			}
			if (empty($model->invoiceable_type)) {
				$model->invoiceable_type = is_null($currentTenant) ? 'App\Models\User' : 'App\Models\Company';
			}
			if (empty($model->invoiceable_id)) {
				$model->invoiceable_id = is_null($currentTenant) ? auth()->id() : $currentTenant->id;
			}
		});
	}
}

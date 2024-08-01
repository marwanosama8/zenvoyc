<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{

	use HasFactory;

	protected $fillable = [
		'invoice_id	',
		'description',
		'amount',
		'type',
		'price',
		'notInvoiced',
		'order_column',
	];

	public function invoice()
	{
		return $this->belongsTo(TenantInvoice::class, 'invoice_id', 'id');
	}

	protected static function booted(): void
	{
		static::creating(function (InvoiceItem $invoiceItem) {
			// dd($invoiceItem);
			// 	if (empty($invoiceItem->price)) {
		// 		$rate = $invoiceItem->invoice->customer->rate;
		// 		dd($rate);
		// 		$invoiceItem->price = TenantInvoice::getPriceAmount($invoiceItem->type, $invoiceItem->amount, $rate);
		// 	}
		});

		// static::updating(function (InvoiceItem $invoiceItem) {
		// 	if (empty($invoiceItem->price)) {
		// 		$rate = $invoiceItem->invoice->customer->rate;
		// 		$invoiceItem->price = TenantInvoice::getPriceAmount($invoiceItem->type, $invoiceItem->amount, $rate);
		// 	}
		// });
	}
}

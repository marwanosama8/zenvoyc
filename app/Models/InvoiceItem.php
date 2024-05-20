<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{

    use HasFactory;

	protected $rules = [
		'customer_id' => 'required',
	];
	protected $guarded = [];

	public function Invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	protected static function booted(): void
    {
        static::creating(function (InvoiceItem $invoiceItem) {
			$rate = $invoiceItem->invoice->customer->rate;
			$invoiceItem->price = Invoice::getPriceAmount($invoiceItem->type,$invoiceItem->amount,$rate);
        });

		static::updating(function (InvoiceItem $invoiceItem) {
			$rate = $invoiceItem->invoice->customer->rate;
			$invoiceItem->price = Invoice::getPriceAmount($invoiceItem->type,$invoiceItem->amount,$rate);
        });
    }
}

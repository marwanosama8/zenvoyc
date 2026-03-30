<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceMedia extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'invoice_id', 'content'];

    /**
     * Get the invoice that owns the InvoiceMedia
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(TenantInvoice::class,'invoice_id','id');
    }
}

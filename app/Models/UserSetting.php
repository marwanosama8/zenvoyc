<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'managing_director',
        'legal_name',
        'avatar_url',
        'website_url',
        'place_of_jurisdiction',
        'slug',
        'address',
        'postal_code',
        'tax_id',
        'vat_id',
        'iban',
        'account_number',
        'bank_code',
        'bic',
        'contact_number',
        'contact_email',
        'vat_percent',
        'invoice_language',
        'invoice_theme_id',
        'currency_id',
    ];

    /**
     * Get the user that owns the UserSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

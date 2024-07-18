<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasAvatar;

class Company extends Model implements HasAvatar
{
    use HasFactory;

    protected $fillable = [
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
        'contact_email'
    ];
    /**
     * The users that belong to the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_companies', 'company_id', 'user_id');
    }

    // public function belongsToCompany()
    // {
    //     return $this->belongsTo(Company::class);
    // }

    // public function belongsToManyCompanies()
    // {
    //     return $this->belongsToMany(Company::class, 'users_companies');
    // }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function hasRole($role)
    {
        // Logic to check if the user has a specific role
    }

    public function expenditures()
    {
        return $this->morphMany(Expenditure::class, 'expenditureable');
    }

    public function invoices()
    {
        return $this->morphMany(TenantInvoice::class, 'invoiceable');
    }
    public function autoInvoices()
    {
        return $this->morphMany(AutoInvoice::class, 'autoInvoiceable');
    }

    public function licenses()
    {
        return $this->morphMany(License::class, 'licenseable');
    }

    public function offers()
    {
        return $this->morphMany(Offer::class, 'offerable');
    }

    public function customers()
    {
        return $this->morphMany(Customer::class, 'customerable');
    }
    public function sales()
    {
        return $this->morphMany(Sales::class, 'salesable');
    }
    public function projects()
    {
        return $this->morphMany(Project::class, 'projectable');
    }

    public function timesheets()
    {
        return $this->morphMany(Timesheet::class, 'timesheetable');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }


}

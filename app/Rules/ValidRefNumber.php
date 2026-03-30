<?php

namespace App\Rules;

use App\Models\Company;
use App\Models\Customer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRefNumber implements ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];


    public function __construct(string $tenant)
    {
        $this->tenant = Company::whereSlug($tenant)->first();
    }

    /**
     * The tenant model.
     *
     * @var \App\Models\Company|null
     */
    protected ?Company $tenant = null;



    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->tenant) {
            $fail('The tenant is invalid.');
            return;
        }
        $exists = Customer::withoutGlobalScopes()->where('reference', $value)
            ->where('customerable_type', Company::class)
            ->where('customerable_id', $this->tenant->id)
            ->exists();
        if (!$exists) {
            $fail('The reference number is invalid or not associated with the specified tenant.');
        }
    }
}

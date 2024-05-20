<?php

namespace App\Models\Scopes;

use App\Helpers\TenancyHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SalesScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $currentTenant = TenancyHelpers::getTenant();

        if (is_null($currentTenant)) {
            // retrive auth user customers
            $builder->where('salesable_type', 'App\Models\User')->where('salesable_id', auth()->id());
        } else {
            // retrive auth company customers
            $builder->where('salesable_type', 'App\Models\Company')->where('salesable_id', $currentTenant->id);
        }
    }
}

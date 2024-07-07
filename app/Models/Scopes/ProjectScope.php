<?php

namespace App\Models\Scopes;

use App\Helpers\TenancyHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProjectScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $currentTenant = TenancyHelpers::getTenant();

        if (is_null($currentTenant)) {
            // retrive auth user projects
            $builder->where('projectable_type', 'App\Models\User')->where('projectable_id', auth()->id());
        } else {
            // retrive auth company projects
            $builder->where('projectable_type', 'App\Models\Company')->where('projectable_id', $currentTenant->id);
        }
    }
}

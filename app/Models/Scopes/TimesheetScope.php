<?php

namespace App\Models\Scopes;

use App\Helpers\TenancyHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TimesheetScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $currentTenant = TenancyHelpers::getTenant();

        if (is_null($currentTenant)) {
            // retrive auth user timesheet
            $builder->where('timesheetable_type', 'App\Models\User')->where('timesheetable_id', auth()->id());
        } else {
            // retrive auth company timesheet
            $builder->where('timesheetable_type', 'App\Models\Company')->where('timesheetable_id', $currentTenant->id);
        }
    }
}

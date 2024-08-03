<?php

namespace App\Policies;

use App\Models\AutoInvoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AutoInvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view auto invoice');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AutoInvoice $autoInvoice): bool
    {
        return $user->hasPermissionTo('create auto invoice');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('update auto invoice') && $user->settings()->first() ? $user->settings->ready_to_generate : true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AutoInvoice $autoInvoice): bool
    {
        return $user->hasPermissionTo('update auto invoice');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AutoInvoice $autoInvoice): bool
    {
        return $user->hasPermissionTo('delete auto invoice');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AutoInvoice $autoInvoice): bool
    {
        return $user->hasPermissionTo('delete auto invoice');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AutoInvoice $autoInvoice): bool
    {
        return $user->hasPermissionTo('delete auto invoice');
    }
}

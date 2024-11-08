<?php

namespace App\Services;

use App\Models\Plan;
use Exception;

class NewSubscriperManager
{
    public function assignNewUserToRole($planSlug)
    {
        $plan = Plan::where('slug', $planSlug)->where('is_active', true)->firstOrFail();

        switch ($plan->product->role->name) {
            case 'user':
                $this->userRole($plan->product->role);
                break;

            case 'company':
                $this->companyRole($plan->product->role);
                break;

            case 'super_company':
                $this->superCompanyRole($plan->product->role);
                break;

            case 'employee':
                # code...
                break;

            default:
                # code...
                break;
        }
    }


    private function userRole($roleModel)
    {
        try {
            $user = auth()->user();

            $user->assignRole($roleModel);

            if (!$user->settings) {
                $user->settings()->create();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function companyRole($roleModel)
    {
        try {
            $user = auth()->user();

            $user->assignRole($roleModel);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function superCompanyRole($roleModel)
    {
        try {
            $user = auth()->user();

            $user->assignRole($roleModel);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UserManager
{
    private $user;

    public function createUser(array $data, $role)
    {
        try {
            $this->user = User::create($data);

            
            if (is_string($role)) {
                $this->assignRole($role);
            } elseif (is_array($role)) {
                foreach ($role as $singleRole) {
                    $this->assignRole($singleRole);
                }
            } else {
                throw new InvalidArgumentException("Invalid role type");
            }

            return $this->user;
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            throw new \Exception($th->getMessage());
        }
    }

    private function assignRole($role)
    {
        switch ($role) {
            case 'user':
                $this->userRoleCreation();
                break;
            case 'dashboard':
                $this->dashboardRoleCreation();
                break;
            case 'company':
                $this->companyRoleCreation();
                break;
            case 'super_company':
                $this->superCompanyRoleCreation();
                break;
            case 'employee':
                $this->employeeRoleCreation();
                break;
            case 'admin':
                $this->adminRoleCreation();
                break;
            default:
                throw new InvalidArgumentException("Error: Invalid role - $role");
        }
    }

    public function assignUserToCompany(User $user, $company_id)
    {
        return $user->companies()->attach($company_id);
    }

    private function userRoleCreation()
    {
        $this->user->assignRole('user');
    }

    private function companyRoleCreation()
    {
        $this->user->assignRole('company');
    }

    private function superCompanyRoleCreation()
    {
        $this->user->assignRole('super_company');
    }

    private function adminRoleCreation()
    {
        $this->user->assignRole('admin');
        $this->user->assignRole('super_admin');
    }

    private function dashboardRoleCreation()
    {
        $this->user->assignRole('dashboard');
    }

    private function employeeRoleCreation()
    {
        $this->user->assignRole('employee');
    }
}


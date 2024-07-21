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

    public function createUser(array $data,$role)
    {
        try {
            $this->user = User::create($data);

            switch ($role) {
                case 'user':
                    $this->userRoleCreation();
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
                    throw new InvalidArgumentException("error : $role");
            }

            return $this->user;
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            throw new \Exception($th->getMessage());
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
    }

    private function employeeRoleCreation()
    {
        $this->user->assignRole('employee');
    }
}

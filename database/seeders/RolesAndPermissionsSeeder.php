<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->resetCachedPermissions();
        $this->createPermissions();
        $this->createRoles();
    }

    /**
     * Reset cached roles and permissions.
     */
    private function resetCachedPermissions(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Create permissions.
     */
    private function createPermissions(): void
    {
        $permissions = [
            'create users', 'update users', 'delete users', 'view users',
            'create roles', 'update roles', 'delete roles', 'view roles',
            'create products', 'update products', 'delete products', 'view products',
            'create plans', 'update plans', 'delete plans', 'view plans',
            'create subscriptions', 'update subscriptions', 'delete subscriptions', 'view subscriptions',
            'create one time products', 'update one time products', 'delete one time products', 'view one time products',
            'create discounts', 'update discounts', 'delete discounts', 'view discounts',
            'create blog posts', 'update blog posts', 'delete blog posts', 'view blog posts',
            'create blog post categories', 'update blog post categories', 'delete blog post categories', 'view blog post categories',
            'create roadmap items', 'update roadmap items', 'delete roadmap items', 'view roadmap items',
            'view transactions',
            'update settings',
            'view stats',
            // new permissions
            'create invoice', 'update invoice', 'delete invoice', 'view invoice',
            'create auto invoice', 'update auto invoice', 'delete auto invoice', 'view auto invoice',
            'create offers', 'update offers', 'delete offers', 'view offers',
            'create licences', 'update licences', 'delete licences', 'view licences',
            'create expenditures', 'update expenditures', 'delete expenditures', 'view expenditures',
            'create company', 'update company', 'delete company', 'view company',
            'create multi companies',
            'create time tracking', 'update time tracking', 'delete time tracking', 'view time tracking',
            'create employees', 'update employees', 'delete employees', 'view employees',
            'create tasks', 'update tasks', 'delete tasks', 'view tasks',
            'create task comments', 'update task comments', 'delete task comments', 'view task comments',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
    }

    /**
     * Create roles.
     */
    private function createRoles(): void
    {
        $this->createAdminRole();
        $this->createSuperCompanyRole();
        $this->createCompanyRole();
        $this->createUserRole();
        $this->createEmployeeRole();
    }

    /**
     * Create admin role and assign all permissions.
     */
    private function createAdminRole(): void
    {
        $role = Role::findOrCreate('admin');
        $role->givePermissionTo(Permission::all());
    }

    /**
     * Create super company role and assign appropriate permissions.
     */
    private function createSuperCompanyRole(): void
    {
        $permissions = [
            'create invoice', 'update invoice', 'delete invoice', 'view invoice',
            'create auto invoice', 'update auto invoice', 'delete auto invoice', 'view auto invoice',
            'create offers', 'update offers', 'delete offers', 'view offers',
            'create licences', 'update licences', 'delete licences', 'view licences',
            'create expenditures', 'update expenditures', 'delete expenditures', 'view expenditures',
            'create time tracking', 'update time tracking', 'delete time tracking', 'view time tracking',
            'create company', 'update company', 'delete company', 'view company',
            'create multi companies',
            'create employees', 'update employees', 'delete employees', 'view employees',
            'create tasks', 'update tasks', 'delete tasks', 'view tasks',
            'create task comments', 'update task comments', 'delete task comments', 'view task comments',
        ];

        $superCompany = Role::findOrCreate('super_company');
        $superCompany->syncPermissions($this->getPermissions($permissions));
    }

    /**
     * Create company role.
     */
    private function createCompanyRole(): void
    {
        $permissions = [
            'create invoice', 'update invoice', 'delete invoice', 'view invoice',
            'create auto invoice', 'update auto invoice', 'delete auto invoice', 'view auto invoice',
            'create offers', 'update offers', 'delete offers', 'view offers',
            'create licences', 'update licences', 'delete licences', 'view licences',
            'create company', 'update company', 'delete company', 'view company',
            'create expenditures', 'update expenditures', 'delete expenditures', 'view expenditures',
            'create time tracking', 'update time tracking', 'delete time tracking', 'view time tracking',
            'create employees', 'update employees', 'delete employees', 'view employees',
            'create tasks', 'update tasks', 'delete tasks', 'view tasks',
            'create task comments', 'update task comments', 'delete task comments', 'view task comments',
        ];

        $company = Role::findOrCreate('company');
        $company->syncPermissions($this->getPermissions($permissions));
    }

    /**
     * Create user role.
     */
    private function createUserRole(): void
    {
        $permissions = [
            'create invoice', 'update invoice', 'delete invoice', 'view invoice',
            'create auto invoice', 'update auto invoice', 'delete auto invoice', 'view auto invoice',
            'create offers', 'update offers', 'delete offers', 'view offers',
            'create licences', 'update licences', 'delete licences', 'view licences',
            'create expenditures', 'update expenditures', 'delete expenditures', 'view expenditures',
        ];

        $user = Role::findOrCreate('user');
        $user->syncPermissions($this->getPermissions($permissions));
    }

    /**
     * Create employee role.
     */
    private function createEmployeeRole(): void
    {
        $permissions = [
            'create time tracking', 'update time tracking', 'delete time tracking', 'view time tracking',
            'update tasks', 'view tasks',
            'create task comments', 'view task comments',
        ];

        $employee = Role::findOrCreate('employee');
        $employee->syncPermissions($this->getPermissions($permissions));
    }

    /**
     * Get permission instances by names.
     *
     * @param array $permissions
     * @return array
     */
    private function getPermissions(array $permissions): array
    {
        return array_map(function ($permission) {
            return Permission::findOrCreate($permission);
        }, $permissions);
    }
}

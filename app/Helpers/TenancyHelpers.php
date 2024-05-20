<?php

namespace App\Helpers;

use App\Models\Company;
use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class TenancyHelpers
{
    public static function getTenant()
    {
        return Filament::getTenant();
    }


    public static function getPluckCustomers()
    {
       return is_null(Filament::getTenant()) ? auth()->user()->customers->pluck('name', 'id') : Filament::getTenant()->customers->pluck('name', 'id');
    }

    public static function getPluckSales()
    {
       return is_null(Filament::getTenant()) ? auth()->user()->sales->pluck('name', 'id') : Filament::getTenant()->sales->pluck('name', 'id');
    }

    public static function getEmployeeRoles()
    {
       return Role::findByName('employee');
    }
}

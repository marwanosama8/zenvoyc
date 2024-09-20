<?php

namespace App\Helpers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
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

   public static function getCurrentPanelName()
   {
      return Filament::getCurrentPanel()->getId();
   }

   public static function getCurrentVat()
   {
      return Filament::getTenant()->settings->vat_percent;
   }


   public static function getPluckCustomers()
   {
      return is_null(Filament::getTenant()) ? auth()->user()->customers->pluck('name', 'id') : Filament::getTenant()->customers->pluck('name', 'id');
   }

   public static function getPluckContacts()
   {
      return is_null(Filament::getTenant()) ? auth()->user()->customer_contact->pluck('name', 'id') : Filament::getTenant()->customer_contact->pluck('name', 'id');
   }

   public static function getPluckEmployeeTasks()
   {
      return Task::authEmployeeTasks()->get()->pluck('title', 'id')->toArray();
   }
   public static function getCompanyEmployeeIds()
   {
      return self::getTenant()->users();
   }
   public static function getPluckCompanyEmployeeNames()
   {
      return self::getTenant()->users()->get()->pluck('name','id')->toArray();
   }
   public static function getPluckTasksByProjectKey()
   {
      $projects = Project::all();

      $groupedOptions = [];
      if (self::getCurrentPanelName() == 'employee') {
         $baseQuery = Task::authEmployeeTasks();
      } else {
         if (is_null(Filament::getTenant())) {
            $baseQuery = auth()->user()->tasks();
         } else {
            $baseQuery = Filament::getTenant()->tasks();
         }
      }
      // $query = auth()->user()->tasks();
      
      foreach ($projects as $project) {
         $tasks = (clone $baseQuery)
         ->where('project_id', $project->id)
         ->pluck('title', 'id')
         ->toArray();
         if (!empty($tasks)) {
            $groupedOptions[$project->name] = $tasks;
         }
      }
      return $groupedOptions;
   }
   public static function getPluckProjects()
   {
      return is_null(Filament::getTenant()) ? auth()->user()->projects->pluck('name', 'id') : Filament::getTenant()->projects->pluck('name', 'id');
   }

   public static function getPluckSales()
   {
      return is_null(Filament::getTenant()) ? auth()->user()->sales->pluck('name', 'id') : Filament::getTenant()->sales->pluck('name', 'id');
   }
   public static function getPluckCompanyEmployees()
   {
      if (is_null(Filament::getTenant())) {
         return [];
      }
      return  Filament::getTenant()->users()->whereHas('roles', function ($query) {
         $query->where('name', 'employee');
      })->get()->pluck('name', 'id');
   }

   public static function getEmployeeRoles()
   {
      return Role::findByName('employee');
   }

   public static function isEmployee()
   {
      return auth()->user()->hasRole('employee');
   }
   
   public static function isCompanyOrUser()
   {
      return is_null(Filament::getTenant()) ? 'user' : 'company';
   }

   public static function getTenantModelOutSideFilament()
   {
       $tenantData = session('tenant_data');
   
       if ($tenantData) {
           $tenantClass = $tenantData['class'];
           $tenantId = $tenantData['id'];
   
           $tenantInstance = $tenantClass::find($tenantId);
   
           return $tenantInstance;
       }
   
       return null;
   }
}

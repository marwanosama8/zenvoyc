<?php

namespace App\Filament\Company\Resources\EmployeeResource\Pages;

use App\Filament\Company\Resources\EmployeeResource;
use App\Helpers\TenancyHelpers;
use App\Services\UserManager;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{

    protected static string $resource = EmployeeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $userManger = new UserManager();

        $user = $userManger->createUser($data, 'employee');

        $userManger->assignUserToCompany($user, TenancyHelpers::getTenant()->id);

        return $user;
    }
}

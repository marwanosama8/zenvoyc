<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Enums\RoleEnums;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\CrudDefaults;
use App\Helpers\TenancyHelpers;
use App\Models\User;
use App\Services\UserManager;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    use CrudDefaults;
    protected static string $resource = UserResource::class;
}

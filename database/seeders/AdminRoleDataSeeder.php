<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UserManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRoleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@test.com')->first();
        if (! $adminUser) {
            $userManager = new UserManager();

            $data = [
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'name' => 'Admin',
                'public_name' => 'John Doe',
                'is_admin' => true,
            ];
            $adminUser = $userManager->createUser($data, 'admin');
        }
    }
}

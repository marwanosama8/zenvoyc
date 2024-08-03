<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // $admin = User::find(1)->update([
        //     'password' => bcrypt('admin'),
        // ]);

        $this->callOnce([
            IntervalsSeeder::class,
            CurrenciesSeeder::class,
            OAuthLoginProviderSeeder::class,
            PaymentProvidersSeeder::class,
            RolesAndPermissionsSeeder::class,
            InvoiceThemeSeeder::class,
            // EmailProviderSeeder::class,
        ]);
    }
}

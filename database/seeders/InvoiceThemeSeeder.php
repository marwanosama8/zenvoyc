<?php

namespace Database\Seeders;

use App\Models\InvoiceTheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (InvoiceTheme::all()->count() > 0) {
            return;
        }

        $invoiceThemes = [
            [
                'id' => 1,
                'aliases' => 'pro-tool',
                'name' => 'Pro-Tool',
                'colors' => ['#1e40af', '#fcfcfc', '#000000'],
            ],
            [
                'id' => 2,
                'aliases' => 'pro-tool-2',
                'name' => 'Pro-Tool 2',
                'colors' => ['#1fdd28', '#fcfcfc', '#000000'],
            ],

        ];
        foreach ($invoiceThemes as  $theme) {
            InvoiceTheme::create($theme);
        }
    }
}

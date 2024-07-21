<?php

namespace Database\Factories;

use App\Models\TenantInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TenantInvoice>
 */
class TenantInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rgnr' => TenantInvoice::getNextNr(),
            'invoice_number' => now()->format('Y') . rand(2000, 9999),
            'date_origin' => now(),
            'date_start' => now(),
            'date_end' =>  now()->addMonths(rand(2, 10)),
            'date_pay' =>  now()->addMonths(rand(2, 10)),
            'info' => fake('de_DE')->sentence(),
        ];
    }
}

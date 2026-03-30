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
            'customer_address' => fake('de_DE')->address(),
            'date_origin' => fake()->dateTimeBetween('-1 year', 'now'),
            'vat_percent' => 14.00,
            'date_start' => now(),
            'date_end' =>  now()->addMonths(rand(2, 10)),
            'date_pay' =>  now()->addMonths(rand(2, 10)),
            'info' => fake('de_DE')->sentence(),
        ];
    }
}

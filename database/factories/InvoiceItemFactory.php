<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake('de_DE')->sentence(),
            'amount' => fake('de_DE')->randomFloat(2, 10, 100),
            'price' => fake('de_DE')->randomFloat(2, 10, 100),
        ];
    }
}

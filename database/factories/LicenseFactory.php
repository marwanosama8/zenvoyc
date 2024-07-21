<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('de_DE')->word(),
            'total_volume' => fake('de_DE')->randomFloat(2, 0, 1000),
            'remaining_volume' => fake('de_DE')->randomFloat(2, 0, 1000),
            'price' => fake('de_DE')->randomFloat(2, 0, 1000),
        ];
    }
}

<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expenditure>
 */
class ExpenditureFactory extends Factory
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
            'description' => fake('de_DE')->sentence(),
            'cost' => fake('de_DE')->randomFloat(2, 10, 10000),
            'frequency' => fake('de_DE')->randomElement(['one-time', 'monthly', 'yearly']),
            'start' => fake('de_DE')->dateTimeBetween('-1 year', 'now'),
            'end' => fake('de_DE')->dateTimeBetween('now', '+1 year'),
        ];
    }

    
}

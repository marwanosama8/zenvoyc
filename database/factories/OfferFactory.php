<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => Str::random(20),
            'title' => fake()->sentence(),
            'introtext' => fake()->paragraph(),
            'positions' => [
                'type' => '1',
                'price' => '80',
                'amount' => '10',
                'description' => 'Lorem ipsum dolor sit amet consectetur'
            ],
        ];
    }
}

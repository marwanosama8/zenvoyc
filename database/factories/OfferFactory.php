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
            'offer_value' => rand(1000, 4000),
            'positions' => [
                [
                    'type' => '2',
                    'price' => '80',
                    'amount' => '3',
                    'description' => 'Lorem ipsum dolor sit amet consectetur.'
                ],
                [
                    'type' => '1',
                    'price' => '50',
                    'amount' => '8',
                    'description' => 'Fusce sollicitudin ad ante euismod, aptent integer nulla.'
                ],
                [
                    'type' => '1',
                    'price' => '1000',
                    'amount' => '2',
                    'description' => 'Congue nibh porta mattis varius, primis sem risus.'
                ],
            ],
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('de_DE')->name(),
            'street' => fake('de_DE')->streetName(),
            'nr' => fake('de_DE')->buildingNumber(),
            'zip' => fake('de_DE')->postcode(),
            'city' => fake('de_DE')->city(),
            'country' => fake('de_DE')->country(),
            'email' => fake('de_DE')->safeEmail(),
            'token' => fake('de_DE')->uuid(),
            'cc' => fake('de_DE')->creditCardNumber(),
            'contact' => fake('de_DE')->phoneNumber(),
            'rate' => fake('de_DE')->randomFloat(2, 0, 100),
            'vatid' => fake('de_DE')->regexify('[A-Z]{2}[0-9]{9}'),
        ];
    }
}

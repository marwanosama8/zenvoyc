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
            'reference' => "REF-" . rand(1111, 9999) . '-' . now()->year,
            'city' => fake('de_DE')->city(),
            'country_id' => 51, // 'Germany' Id from countries table            
            'email' => fake('de_DE')->safeEmail(),
            'token' => fake('de_DE')->uuid(),
            'cc' => fake('de_DE')->creditCardNumber(),
            'contact' => fake('de_DE')->phoneNumber(),
            'rate' => fake('de_DE')->randomFloat(2, 0, 100),
            'vat_id' => "DE20111340209",
        ];
    }
}

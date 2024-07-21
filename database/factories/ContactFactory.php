<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
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
            'email' => fake('de_DE')->safeEmail(),
            'phone' => fake('de_DE')->phoneNumber(),
            'function' => fake('de_DE')->jobTitle(),
            'company' => fake('de_DE')->company(),
        ];
    }
}

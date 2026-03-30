<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('de_DE')->company(),
            'managing_director' => fake('de_DE')->name(),
            'legal_name' => fake('de_DE')->company(),
            'avatar_url' => fake('de_DE')->imageUrl(),
            'website_url' => fake('de_DE')->url(),
            'place_of_jurisdiction' => fake('de_DE')->city(),
            'slug' => Str::slug(fake('de_DE')->company()),
            'address' => fake('de_DE')->address(),
            'postal_code' => fake('de_DE')->postcode(),
            'tax_id' => fake('de_DE')->regexify('[A-Z0-9]{10}'),
            'vat_id' => fake('de_DE')->regexify('[A-Z]{2}[0-9]{9}'),
            'iban' => fake('de_DE')->iban(),
            'account_number' => fake('de_DE')->bankAccountNumber(),
            'bank_code' => fake('de_DE')->regexify('[0-9]{8}'),
            'bic' => fake('de_DE')->swiftBicNumber(),
            'contact_number' => fake('de_DE')->phoneNumber(),
            'contact_email' => fake('de_DE')->companyEmail(),
        ];
    }
}

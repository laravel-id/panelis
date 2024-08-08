<?php

namespace Database\Factories\Location;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alpha2' => $this->faker->countryCode,
            'alpha3' => $this->faker->countryISOAlpha3,
            'un_code' => $this->faker->countryCode,
            'name' => $this->faker->country,
        ];
    }
}

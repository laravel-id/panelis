<?php

namespace Database\Factories\Event;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event\Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'logo' => null,
            'name' => $name = fake()->company,
            'slug' => Str::slug($name),
            'description' => fake()->text(150),
            'phone' => fake()->phoneNumber,
            'email' => fake()->companyEmail,
            'website' => fake()->url,
            'address' => fake()->address,
        ];
    }
}

<?php

namespace Database\Factories\Event;

use App\Models\Location\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'district_id' => District::factory(),
            'slug' => $this->faker->slug,
            'title' => $this->faker->title,
            'description' => $this->faker->text(250),
            'url' => $this->faker->url,
            'started_at' => $this->faker->dateTime,
            'location' => $this->faker->city,
            'categories' => ['5K', '10K', '21K', '42K'],
            'contacts' => [
                [
                    'name' => $this->faker->name,
                    'phone' => $this->faker->phoneNumber,
                    'email' => $this->faker->email,
                ],
            ],
            'metadata' => [
                'source' => $this->faker->url(),
            ],
        ];
    }
}

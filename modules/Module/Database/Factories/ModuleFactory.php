<?php

namespace Modules\Module\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Module\Models\Module;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Module>
 */
class ModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'is_enabled' => $this->faker->boolean(),
        ];
    }
}

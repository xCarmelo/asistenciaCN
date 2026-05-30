<?php

namespace Database\Factories;

use App\Models\Maestro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maestro>
 */
class MaestroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'estado' => $this->faker->randomElement([0, 1]),
            'genero' => $this->faker->randomElement(['M', 'F']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
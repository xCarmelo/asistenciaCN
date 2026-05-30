<?php

namespace Database\Factories;

use App\Models\Corte;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Corte>
 */
class CorteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['Primer Corte', 'Segundo Corte', 'Tercer Corte']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
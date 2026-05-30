<?php

namespace Database\Factories;

use App\Models\Seccion;
use App\Models\Maestro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seccion>
 */
class SeccionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['10° A', '10° B', '11° A', '11° B']),
            'id_maestro_guia' => Maestro::factory(), // Relación: crea un maestro automáticamente
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
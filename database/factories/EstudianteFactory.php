<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\Seccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'nombre_lista' => $this->faker->lastName() . ', ' . $this->faker->firstName(),
            'genero' => $this->faker->randomElement(['M', 'F']),
            'año' => $this->faker->numberBetween(9, 11),
            'id_seccion' => Seccion::factory(), // Relación: crea una sección automáticamente
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo', 'Retirado']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
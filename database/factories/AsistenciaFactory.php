<?php

namespace Database\Factories;

use App\Models\Asistencia;
use App\Models\Seccion;
use App\Models\Estudiante;
use App\Models\Corte;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asistencia>
 */
class AsistenciaFactory extends Factory
{
    public function definition(): array
    {
        $asis = $this->faker->randomElement(['P', 'A']);
        return [
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'id_seccion' => Seccion::factory(),
            'asis' => $asis,
            'justificado' => $asis === 'A' ? $this->faker->boolean(30) : false,
            'injustificado' => $asis === 'A' ? !$this->faker->boolean(30) : false,
            'id_estudiante' => Estudiante::factory(),
            'id_corte' => Corte::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
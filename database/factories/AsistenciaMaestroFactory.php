<?php

namespace Database\Factories;

use App\Models\AsistenciaMaestro;
use App\Models\Maestro;
use App\Models\Corte;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AsistenciaMaestro>
 */
class AsistenciaMaestroFactory extends Factory
{
    public function definition(): array
    {
        $asis = $this->faker->randomElement(['P', 'A']);
        return [
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'id_maestro' => Maestro::factory(),
            'asis' => $asis,
            'justificado' => $asis === 'A' ? $this->faker->boolean(30) : false,
            'injustificado' => $asis === 'A' ? !$this->faker->boolean(30) : false,
            'id_corte' => Corte::factory(),
            'tutelado' => $asis === 'A' ? $this->faker->sentence(3) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
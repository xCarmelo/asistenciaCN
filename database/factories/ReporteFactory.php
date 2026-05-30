<?php

namespace Database\Factories;

use App\Models\Reporte;
use App\Models\Seccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reporte>
 */
class ReporteFactory extends Factory
{
    public function definition(): array
    {
        $totalMujeres = $this->faker->numberBetween(10, 20);
        $totalHombres = $this->faker->numberBetween(10, 20);
        return [
            'id_seccion' => Seccion::factory(),
            'cef' => $totalMujeres,
            'cem' => $totalHombres,
            'crf' => $this->faker->numberBetween(0, $totalMujeres),
            'crm' => $this->faker->numberBetween(0, $totalHombres),
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
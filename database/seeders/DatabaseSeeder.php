<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MaestroSeeder::class,
            CorteSeeder::class,
            SeccionSeeder::class,
            EstudianteSeeder::class,
            // AsistenciaSeeder::class,       // coméntalos si dan error
            // AsistenciaMaestroSeeder::class,
            // ReporteSeeder::class,
        ]);
    }
}

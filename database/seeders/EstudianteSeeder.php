<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // No truncamos porque la tabla ya está vacía después de migrate:fresh
        // O si queremos asegurar, podemos eliminar los registros manualmente:
        // DB::table('estudiantes')->delete();  // Esto sí funciona con FK, a diferencia de truncate

        $secciones = DB::table('secciones')->pluck('id')->toArray();
        if (empty($secciones)) {
            $secciones = [1, 2, 3, 4];
        }

        $estudiantes = [
            ['name' => 'Ana Pérez', 'numero_lista' => 1, 'genero' => 'F', 'año' => 10, 'id_seccion' => $secciones[0], 'estado' => 'Activo'],
            ['name' => 'Luis Gómez', 'numero_lista' => 2, 'genero' => 'M', 'año' => 10, 'id_seccion' => $secciones[0], 'estado' => 'Activo'],
            ['name' => 'Sofía Rodríguez', 'numero_lista' => 3, 'genero' => 'F', 'año' => 10, 'id_seccion' => $secciones[0], 'estado' => 'Activo'],
            ['name' => 'Mateo Fernández', 'numero_lista' => 4, 'genero' => 'M', 'año' => 10, 'id_seccion' => $secciones[0], 'estado' => 'Activo'],
            ['name' => 'Valentina López', 'numero_lista' => 1, 'genero' => 'F', 'año' => 10, 'id_seccion' => $secciones[1], 'estado' => 'Activo'],
            ['name' => 'Santiago Díaz', 'numero_lista' => 2, 'genero' => 'M', 'año' => 10, 'id_seccion' => $secciones[1], 'estado' => 'Activo'],
            ['name' => 'Isabella Torres', 'numero_lista' => 3, 'genero' => 'F', 'año' => 10, 'id_seccion' => $secciones[1], 'estado' => 'Activo'],
            ['name' => 'Nicolás Sánchez', 'numero_lista' => 1, 'genero' => 'M', 'año' => 11, 'id_seccion' => $secciones[2], 'estado' => 'Activo'],
            ['name' => 'Camila Ramírez', 'numero_lista' => 2, 'genero' => 'F', 'año' => 11, 'id_seccion' => $secciones[2], 'estado' => 'Activo'],
            ['name' => 'Andrés Castro', 'numero_lista' => 3, 'genero' => 'M', 'año' => 11, 'id_seccion' => $secciones[2], 'estado' => 'Activo'],
            ['name' => 'Lucía Morales', 'numero_lista' => 1, 'genero' => 'F', 'año' => 11, 'id_seccion' => $secciones[3], 'estado' => 'Activo'],
            ['name' => 'Diego Ortega', 'numero_lista' => 2, 'genero' => 'M', 'año' => 11, 'id_seccion' => $secciones[3], 'estado' => 'Activo'],
        ];

        foreach ($estudiantes as $est) {
            DB::table('estudiantes')->insert(array_merge($est, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('secciones')->insert([
            [
                'nombre' => '10° A',
                'id_maestro_guia' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => '10° B',
                'id_maestro_guia' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => '11° A',
                'id_maestro_guia' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => '11° B',
                'id_maestro_guia' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
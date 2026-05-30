<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MaestroSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('maestros')->insert([
            [
                'name' => 'Carlos Mendoza',
                'estado' => 1,
                'genero' => 'M',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'María González',
                'estado' => 1,
                'genero' => 'F',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'José Ramírez',
                'estado' => 1,
                'genero' => 'M',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ana Martínez',
                'estado' => 0,
                'genero' => 'F',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cortes')->insert([
            [
                'nombre' => 'I Corte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'II Corte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'III Corte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'IV Corte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('tipos_asistencia', function (Blueprint $table) {
            $table->id();
            $table->char('codigo', 1)->unique();
            $table->string('nombre', 20);
            $table->boolean('es_presente')->default(false);
            $table->timestamps();
        });
        DB::table('tipos_asistencia')->insert([
            ['codigo' => 'P', 'nombre' => 'Presente', 'es_presente' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'A', 'nombre' => 'Ausente', 'es_presente' => false, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'J', 'nombre' => 'Justificado', 'es_presente' => false, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'T', 'nombre' => 'Llegada tarde', 'es_presente' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down() { Schema::dropIfExists('tipos_asistencia'); }
};

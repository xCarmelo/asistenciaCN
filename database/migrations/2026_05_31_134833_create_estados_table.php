<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->boolean('permite_asistencia')->default(false);
            $table->boolean('visible_en_listados')->default(true);
            $table->timestamps();
        });
        DB::table('estados')->insert([
            ['nombre' => 'Activo', 'permite_asistencia' => true, 'visible_en_listados' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Inactivo', 'permite_asistencia' => false, 'visible_en_listados' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Retirado', 'permite_asistencia' => false, 'visible_en_listados' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Graduado', 'permite_asistencia' => false, 'visible_en_listados' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down() { Schema::dropIfExists('estados'); }
};

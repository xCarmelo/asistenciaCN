<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('asistencias_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_estudiante_id')->constrained('historial_estudiantes')->onDelete('cascade');
            $table->date('fecha');
            $table->foreignId('id_corte')->constrained('cortes');
            $table->foreignId('id_tipo_asistencia')->constrained('tipos_asistencia');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('asistencias_estudiantes'); }
};

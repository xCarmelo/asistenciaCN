<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('id_seccion')->constrained('secciones');
            $table->char('asis', 1);
            $table->boolean('justificado')->default(false);
            $table->boolean('injustificado')->default(false);
            $table->foreignId('id_estudiante')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('id_corte')->constrained('cortes');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('asistencias'); }
};

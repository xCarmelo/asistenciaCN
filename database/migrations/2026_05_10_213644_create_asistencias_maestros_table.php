<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('asistencias_maestros', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('id_maestro')->constrained('maestros')->onDelete('cascade');
            $table->char('asis', 1);
            $table->boolean('justificado')->default(false);
            $table->boolean('injustificado')->default(false);
            $table->foreignId('id_corte')->constrained('cortes');
            $table->string('tutelado', 50)->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('asistencias_maestros'); }
};

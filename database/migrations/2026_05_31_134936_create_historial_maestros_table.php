<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('historial_maestros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maestro_id')->constrained('maestros')->onDelete('cascade');
            $table->foreignId('seccion_id')->nullable()->constrained('secciones')->onDelete('set null');
            $table->foreignId('estado_id')->constrained('estados')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('historial_maestros'); }
};

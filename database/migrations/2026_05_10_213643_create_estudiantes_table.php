<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('numero_lista')->nullable();
            $table->char('genero', 1)->nullable();
            $table->integer('año')->nullable();
            $table->foreignId('id_seccion')->nullable()->constrained('secciones')->nullOnDelete();
            $table->string('estado', 20)->nullable()->default('Activo');
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('estudiantes'); }
};

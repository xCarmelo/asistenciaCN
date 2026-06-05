<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_seccion')->nullable()->constrained('secciones')->onDelete('cascade');
            $table->enum('tipo', ['estudiante', 'maestro'])->default('estudiante');
            $table->foreignId('id_maestro')->nullable()->constrained('maestros')->onDelete('cascade');
            $table->integer('cef')->default(0);
            $table->integer('cem')->default(0);
            $table->integer('crf')->default(0);
            $table->integer('crm')->default(0);
            $table->date('fecha');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('reportes'); }
};

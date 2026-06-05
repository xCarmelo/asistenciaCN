<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('maestros', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('estado_general')->default(1);
            $table->char('genero', 1)->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('maestros'); }
};
